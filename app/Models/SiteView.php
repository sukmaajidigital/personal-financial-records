<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class SiteView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ip_hash',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'date',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Generate a secure, irreversible hash of an IP address.
     * Uses HMAC-SHA256 with the app key as secret to prevent rainbow table attacks.
     */
    public static function hashIp(string $ip): string
    {
        return hash_hmac('sha256', $ip, config('app.key'));
    }

    /**
     * Record a unique view for the given IP address (once per day).
     * Uses insertOrIgnore to handle race conditions safely.
     */
    public static function recordView(string $ip): void
    {
        static::insertOrIgnore([
            'ip_hash' => static::hashIp($ip),
            'viewed_at' => now()->toDateString(),
            'created_at' => now(),
        ]);
    }

    /**
     * Get total unique visitor count (unique IPs across all time).
     */
    public static function totalUniqueVisitors(): int
    {
        return static::distinct('ip_hash')->count('ip_hash');
    }

    /**
     * Get total page view count (all views).
     */
    public static function totalViews(): int
    {
        return static::count();
    }
}
