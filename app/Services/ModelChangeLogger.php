<?php

namespace App\Services;

use App\Models\Log;
use App\Traits\AdminHelperTrait;
use App\Traits\AgentTrait;
use Illuminate\Database\Eloquent\Model;

class ModelChangeLogger
{
    use AdminHelperTrait, AgentTrait;

    protected array $skip_tables = [
        'logs', 'activity_logs', 'user_devices', 'devices', 'sessions', 'jobs',
        'failed_jobs', 'cache', 'cache_locks', 'personal_access_tokens', 'ltm_translations',
    ];

    /** Columns never worth a row, on top of AdminHelperTrait::$log_not_in. */
    protected array $skip_columns = ['last_active', 'remember_token'];

    /** user id => device id, for the life of this request. */
    protected array $device_cache = [];

    public function created(Model $model): void
    {
        if (! $this->shouldLog($model)) {
            return;
        }

        $rows = [];

        foreach ($model->getAttributes() as $key => $value) {
            if ($this->skipColumn($model, $key) || $this->isBlank($value)) {
                continue;
            }

            $rows[] = $this->row($model, $key, null, $value);
        }

        $this->write($rows);
    }

    public function updated(Model $model): void
    {
        if (! $this->shouldLog($model)) {
            return;
        }

        $rows = [];

        // getChanges() is what actually went to the database on this save.
        foreach ($model->getChanges() as $key => $value) {
            if ($this->skipColumn($model, $key)) {
                continue;
            }

            $old = $model->getOriginal($key);

            // A null that became an empty string is not a change worth keeping.
            if ($this->isBlank($old) && $this->isBlank($value)) {
                continue;
            }

            $rows[] = $this->row($model, $key, $old, $value);
        }

        $this->write($rows);
    }

    public function deleted(Model $model): void
    {
        if (! $this->shouldLog($model)) {
            return;
        }

        $original = collect($model->getOriginal())
            ->reject(fn ($value, $key) => $this->skipColumn($model, $key))
            ->all();

        $this->write([
            $this->row($model, 'deleted', json_encode($original, JSON_UNESCAPED_UNICODE), null),
        ]);
    }

    protected function shouldLog(Model $model): bool
    {
        return $this->currentUser() !== null
            && ! in_array($model->getTable(), $this->skip_tables, true);
    }

    protected function skipColumn(Model $model, string $key): bool
    {
        if (in_array($key, $this->log_not_in, true) || in_array($key, $this->skip_columns, true)) {
            return true;
        }

        // Never let a hashed secret reach a table an admin can read.
        return in_array($key, $model->getHidden(), true);
    }

    protected function isBlank($value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

    protected function row(Model $model, string $key, $old, $new): array
    {
        $user = $this->currentUser();
        $now = now();

        return [
            'user_id' => $user->id,
            'user_device_id' => $this->deviceFor($user),
            'loggable_id' => $model->getKey(),
            'loggable_type' => $model->getTable(),
            'key' => $key,
            'value_old' => $this->stringify($old),
            'value' => $this->stringify($new),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    protected function stringify($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }

    protected function write(array $rows): void
    {
        if ($rows !== []) {
            Log::insert($rows);
        }
    }

    protected function currentUser()
    {
        return auth()->user() ?: auth('sanctum')->user();
    }

    protected function deviceFor($user): ?int
    {
        if (! array_key_exists($user->id, $this->device_cache)) {
            $this->device_cache[$user->id] = $this->checkAgent('web', $user);
        }

        return $this->device_cache[$user->id];
    }
}
