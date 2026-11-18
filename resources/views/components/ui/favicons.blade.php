@php
    $favicon = app(\App\Services\SettingsService::class)->url('favicon');
    $type = match (strtolower(pathinfo(parse_url((string) $favicon, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION))) {
        'ico' => 'image/x-icon',
        'webp' => 'image/webp',
        'jpg', 'jpeg' => 'image/jpeg',
        default => 'image/png',
    };
@endphp
<link rel="icon" href="{{ $favicon ?? asset('favicon.png') }}" type="{{ $favicon ? $type : 'image/png' }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<meta name="theme-color" content="#031f4f">
