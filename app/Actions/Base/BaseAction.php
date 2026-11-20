<?php

declare(strict_types=1);

namespace App\Actions\Base;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;


abstract class BaseAction
{
    protected function withTransaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }


    protected function withoutFailing(string $context, Closure $callback): void
    {
        try {
            $callback();
        } catch (Throwable $e) {
            Log::error("Side effect failed: {$context}", [
                'exception' => $e->getMessage(),
                'class' => $e::class,
                'file' => $e->getFile().':'.$e->getLine(),
            ]);
        }
    }
}
