<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class SettingsHelper
{
    /**
     * Obtenir un paramètre configuré.
     */
    public static function get($key, $default = null)
    {
        $settings = [];
        $filePath = storage_path('app/settings.json');

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $settings = json_decode($content, true) ?: [];
        }

        return data_get($settings, $key, $default);
    }

    /**
     * Enregistrer un paramètre de configuration.
     */
    public static function set($key, $value)
    {
        $settings = [];
        $filePath = storage_path('app/settings.json');

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $settings = json_decode($content, true) ?: [];
        }

        data_set($settings, $key, $value);
        file_put_contents($filePath, json_encode($settings, JSON_PRETTY_PRINT));
    }
}
