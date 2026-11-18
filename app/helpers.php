<?php

use App\Services\SettingsService;

if (! function_exists('setting')) {
    /** Get a CMS site setting value. */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}

if (! function_exists('safe_url')) {
    /**
     * Normalise a CMS-entered link. Allows relative paths, anchors and
     * http(s)/mailto/tel URLs; anything else (e.g. javascript:) becomes '#'.
     */
    function safe_url(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '#';
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return url($url);
        }

        if (str_starts_with($url, '#') || preg_match('#^(https?://|mailto:|tel:)#i', $url)) {
            return $url;
        }

        return '#';
    }
}
