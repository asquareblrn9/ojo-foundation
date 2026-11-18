<?php

namespace App\Services;

use App\Models\Setting;
use App\Support\Media;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Read/write access to CMS site settings. All settings are loaded with a
 * single query and cached until the next write.
 */
class SettingsService
{
    public const CACHE_KEY = 'site_settings';

    private ?array $loaded = null;

    public function all(): array
    {
        if ($this->loaded !== null) {
            return $this->loaded;
        }

        $defaults = collect(config('foundation.settings'))->map(fn ($d) => $d['default'])->all();

        try {
            $stored = Cache::rememberForever(self::CACHE_KEY, fn () => Setting::all()
                ->mapWithKeys(fn (Setting $s) => [$s->key => $s->typedValue()])
                ->all());
        } catch (Throwable) {
            // Database not yet available (e.g. during installation).
            $stored = [];
        }

        return $this->loaded = array_merge($defaults, $stored);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /** Public URL for an image-type setting, or null. */
    public function url(string $key): ?string
    {
        $path = $this->get($key);

        return $path ? Media::disk()->url($path) : null;
    }

    /** @param array<string, mixed> $values */
    public function set(array $values): void
    {
        $definitions = config('foundation.settings');

        foreach ($values as $key => $value) {
            $definition = $definitions[$key] ?? ['group' => 'custom', 'type' => 'string'];

            Setting::updateOrCreate(['key' => $key], [
                'group' => $definition['group'],
                'type' => $definition['type'],
                'value' => match ($definition['type']) {
                    'bool' => $value ? '1' : '0',
                    'json' => json_encode($value),
                    default => $value,
                },
            ]);
        }

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->loaded = null;
    }

    /** Non-empty social links keyed by network. */
    public function socialLinks(): array
    {
        return collect(['facebook', 'instagram', 'x', 'linkedin', 'youtube', 'tiktok'])
            ->mapWithKeys(fn ($n) => [$n => $this->get("social_{$n}")])
            ->filter()
            ->all();
    }
}
