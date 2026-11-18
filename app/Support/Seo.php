<?php

namespace App\Support;

use App\Services\SettingsService;
use Illuminate\Support\Str;

/**
 * SEO metadata for a public page. Values fall back to the site-wide
 * defaults managed in Admin → Settings → SEO.
 */
class Seo
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $canonical = null,
        public string $type = 'website',
        public ?string $publishedAt = null,
        public ?string $modifiedAt = null,
        public array $schema = [],
        public bool $noindex = false,
    ) {}

    public static function make(?string $title = null, ?string $description = null, ?string $image = null): self
    {
        return new self($title, $description, $image);
    }

    public function pageTitle(): string
    {
        $site = setting('site_name');

        if (! $this->title) {
            return setting('seo_title') ?: $site;
        }

        return Str::contains($this->title, $site) ? $this->title : "{$this->title} | {$site}";
    }

    public function metaDescription(): string
    {
        return Str::limit(trim(strip_tags($this->description ?: (string) setting('seo_description'))), 160);
    }

    public function imageUrl(): ?string
    {
        return $this->image
            ? ResponsiveImage::variant($this->image, 'lg')
            : app(SettingsService::class)->url('og_image');
    }

    public function canonicalUrl(): string
    {
        return $this->canonical ?: url()->current();
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function article(?string $publishedAt, ?string $modifiedAt): self
    {
        $this->type = 'article';
        $this->publishedAt = $publishedAt;
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function schema(array $schema): self
    {
        $this->schema[] = $schema;

        return $this;
    }

    /** JSON-LD Organization schema included on every page. */
    public static function organizationSchema(): array
    {
        $settings = app(SettingsService::class);

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'NGO',
            'name' => setting('site_name'),
            'url' => url('/'),
            'logo' => $settings->url('logo'),
            'email' => setting('contact_email'),
            'telephone' => setting('contact_phone'),
            'address' => setting('address'),
            'sameAs' => array_values($settings->socialLinks()) ?: null,
        ]);
    }
}
