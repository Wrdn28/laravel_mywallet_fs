<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $table = 'system_config';

    protected $fillable = ['config_key', 'config_value'];

    /**
     * Get a config value by key, with optional default.
     */
    public static function getValue(string $key, string $default = ''): string
    {
        $record = static::where('config_key', $key)->first();

        return $record?->config_value ?? $default;
    }
}
