<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

#[Fillable(['key', 'value'])]
class SystemSetting extends Model
{
    use HasFactory;

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("system_setting:{$key}", function () use ($key, $default) {
            try {
                if (! Schema::hasTable('system_settings')) {
                    return $default;
                }

                $setting = static::where('key', $key)->first();

                return $setting !== null ? $setting->value : $default;
            } catch (\Throwable) {
                return $default;
            }
        });
    }

    public static function set(string $key, mixed $value): void
    {
        $normalized = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $normalized]
        );

        Cache::forget("system_setting:{$key}");
    }

    public static function clearCache(?string $key = null): void
    {
        if ($key !== null) {
            Cache::forget("system_setting:{$key}");
        } else {
            Cache::forget('system_setting:maintenance_mode');
            Cache::forget('system_setting:humas_whatsapp');
        }
    }

    public static function isMaintenanceMode(): bool
    {
        return static::get('maintenance_mode', '0') === '1';
    }

    public static function setMaintenanceMode(bool $enabled): void
    {
        static::set('maintenance_mode', $enabled ? '1' : '0');
    }
}
