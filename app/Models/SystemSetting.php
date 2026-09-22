<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class SystemSetting extends Model
{
    use HasFactory;

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting !== null ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
        );
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
