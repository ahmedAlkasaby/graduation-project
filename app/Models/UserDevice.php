<?php

namespace App\Models;

use App\Builders\BaseBuilder;

/**
 * The browser a logged request came from. Rows are reused rather than
 * appended: see the migration for why the identity is the ip *range*.
 */
class UserDevice extends SiteModelDefault
{
    protected $fillable = [
        'user_id', 'ip', 'ip_range', 'device', 'platform', 'browser', 'is_desktop', 'is_phone',
    ];

    protected $casts = [
        'is_desktop' => 'boolean',
        'is_phone' => 'boolean',
    ];

    public function newEloquentBuilder($query): BaseBuilder
    {
        return new BaseBuilder($query);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /** Human label for the log tables: "Windows / Chrome". */
    public function title(): string
    {
        return trim(implode(' / ', array_filter([$this->platform, $this->browser]))) ?: '-';
    }

    /**
     * The /24 of an IPv4 address, or the /64 prefix of an IPv6 one. Keeps a
     * device stable across the address changes a single connection makes.
     */
    public static function getIpSubnet(?string $ip, int $cidr = 24): ?string
    {
        if (! $ip) {
            return null;
        }

        if (str_contains($ip, ':')) {
            return implode(':', array_slice(explode(':', $ip), 0, 4));
        }

        return implode('.', array_slice(explode('.', $ip), 0, max(1, intdiv($cidr, 8))));
    }

    /**
     * Returns the id of the device row for these attributes, creating it the
     * first time that user is seen on that browser and network.
     */
    public static function updateOrCreateDevice(array $attributes): ?int
    {
        if (empty($attributes['user_id'])) {
            return null;
        }

        $device = static::query()->firstOrNew([
            'user_id' => $attributes['user_id'],
            'platform' => $attributes['platform'] ?? null,
            'browser' => $attributes['browser'] ?? null,
            'ip_range' => static::getIpSubnet($attributes['ip'] ?? null),
        ]);

        $device->fill([
            'ip' => $attributes['ip'] ?? '',
            'device' => $attributes['device'] ?? null,
            'is_desktop' => $attributes['is_desktop'] ?? true,
            'is_phone' => $attributes['is_phone'] ?? false,
        ])->save();

        return $device->id;
    }
}
