<?php

namespace App\Livewire\Admin;

use App\Enums\Permission;
use App\Services\ActivityLogger;
use App\Services\ImageService;
use App\Services\SettingsService;
use App\Support\Concerns\HandlesImageUpload;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use App\Support\Media;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Site settings, generated from config('foundation.settings') and grouped
 * into tabs. Labels, help text and input formats are defined in FIELDS.
 */
#[Layout('layouts.admin')]
#[Title('Settings')]
class Settings extends Component
{
    use HandlesImageUpload, RequiresPermission, WithFileUploads, WithToasts;

    public const TABS = [
        'general' => ['General', 'settings'],
        'contact' => ['Contact', 'map-pin'],
        'social' => ['Social media', 'share-2'],
        'seo' => ['SEO & analytics', 'search'],
        'footer' => ['Footer', 'panel-bottom'],
        'donations' => ['Donations', 'heart'],
        'newsletter' => ['Newsletter', 'mail'],
        'notifications' => ['Notifications', 'bell'],
        'security' => ['Security', 'shield-check'],
    ];

    /** key => [label, help, format] — format: text|textarea|email|url|tel|ga|image */
    public const FIELDS = [
        'site_name' => ['Foundation name', null, 'text'],
        'site_short_name' => ['Short name', 'Used as the monogram when no logo is uploaded.', 'text'],
        'tagline' => ['Tagline', 'Shown beneath the name in the header and footer.', 'text'],
        'logo' => ['Logo (emblem)', 'A square or circular emblem. PNG or WebP with a transparent background works best.', 'image'],
        'logo_light' => ['Logo for dark backgrounds', 'Optional. Used on navy backgrounds if your emblem does not stand out.', 'image'],
        'favicon' => ['Favicon', 'The small icon in the browser tab. A square image of at least 192×192 pixels.', 'image'],
        'contact_email' => ['Contact email', 'Shown on the Contact page and footer.', 'email'],
        'contact_phone' => ['Phone number', null, 'tel'],
        'contact_phone_alt' => ['Alternative phone number', null, 'tel'],
        'address' => ['Address', null, 'textarea'],
        'office_hours' => ['Office hours', 'e.g. Monday – Friday, 9am – 5pm', 'text'],
        'map_embed_url' => ['Google Maps embed URL', 'In Google Maps: Share → Embed a map → copy only the https://www.google.com/maps/embed?… address.', 'url'],
        'social_facebook' => ['Facebook', null, 'url'],
        'social_instagram' => ['Instagram', null, 'url'],
        'social_x' => ['X (Twitter)', null, 'url'],
        'social_linkedin' => ['LinkedIn', null, 'url'],
        'social_youtube' => ['YouTube', null, 'url'],
        'social_tiktok' => ['TikTok', null, 'url'],
        'seo_title' => ['Default page title', 'Used on the home page and when a page has no title of its own.', 'text'],
        'seo_description' => ['Default description', 'About 150 characters, shown in search results.', 'textarea'],
        'seo_keywords' => ['Keywords', 'Comma separated.', 'text'],
        'og_image' => ['Default sharing image', 'Shown when a page is shared on social media. Ideal size 1200×630.', 'image'],
        'google_analytics_id' => ['Google Analytics measurement ID', 'e.g. G-XXXXXXXXXX. Tracking only runs on the live site.', 'ga'],
        'footer_about' => ['Footer description', null, 'textarea'],
        'footer_copyright' => ['Copyright text', 'The year is added automatically.', 'text'],
        'donations_enabled' => ['Enable donations', 'Shows the Donate button and donation section when a link or bank details are provided.', 'bool'],
        'donation_heading' => ['Donation heading', null, 'text'],
        'donation_text' => ['Donation message', null, 'textarea'],
        'donation_url' => ['Online donation link', 'e.g. your PayPal, GoFundMe, Paystack or Givebutter page.', 'url'],
        'donation_bank_details' => ['Bank transfer details', 'Shown exactly as entered.', 'textarea'],
        'newsletter_enabled' => ['Show newsletter sign-up', null, 'bool'],
        'newsletter_double_opt_in' => ['Require email confirmation', 'Subscribers must click a link in a confirmation email (recommended).', 'bool'],
        'newsletter_heading' => ['Newsletter heading', null, 'text'],
        'newsletter_text' => ['Newsletter text', null, 'textarea'],
        'notification_email' => ['Send notifications to', 'Where new enquiries are emailed. Leave empty to notify all Super Admins.', 'email'],
        'notify_contact' => ['New contact messages', null, 'bool'],
        'notify_volunteer' => ['New volunteer applications', null, 'bool'],
        'notify_partnership' => ['New partnership enquiries', null, 'bool'],
        'notify_newsletter' => ['New newsletter subscribers', null, 'bool'],
        'turnstile_enabled' => ['Cloudflare Turnstile on public forms', 'Extra bot protection. Requires TURNSTILE_SITE_KEY and TURNSTILE_SECRET_KEY in the server environment.', 'bool'],
    ];

    #[Url(except: 'general')]
    public string $tab = 'general';

    public array $values = [];

    public array $uploads = [];

    public array $remove = [];

    protected function permission(): Permission
    {
        return Permission::ManageSettings;
    }

    public function mount(SettingsService $settings): void
    {
        if (! isset(self::TABS[$this->tab])) {
            $this->tab = 'general';
        }

        foreach (config('foundation.settings') as $key => $definition) {
            if ($definition['type'] !== 'image') {
                $this->values[$key] = $definition['type'] === 'bool' ? (bool) $settings->get($key) : (string) $settings->get($key);
            }
        }
    }

    /** Settings shown on the current tab. Only these are validated and saved. */
    private function tabDefinitions(): array
    {
        return array_filter(config('foundation.settings'), fn ($d) => $d['group'] === $this->tab);
    }

    protected function rules(): array
    {
        $rules = [];

        foreach ($this->tabDefinitions() as $key => $definition) {
            $format = self::FIELDS[$key][2] ?? 'text';

            $rules[$definition['type'] === 'image' ? "uploads.{$key}" : "values.{$key}"] = match ($format) {
                'image' => $this->imageRule(),
                'bool' => ['boolean'],
                'email' => ['nullable', 'email', 'max:190'],
                'url' => ['nullable', 'url:https,http', 'max:500'],
                'tel' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s().]*$/'],
                'ga' => ['nullable', 'regex:/^(G|UA|GT)-[A-Z0-9-]+$/i'],
                'textarea' => ['nullable', 'string', 'max:2000'],
                default => ['nullable', 'string', 'max:255'],
            };
        }

        if (isset($rules['values.site_name'])) {
            $rules['values.site_name'] = ['required', 'string', 'max:120'];
        }

        if (isset($rules['values.map_embed_url'])) {
            $rules['values.map_embed_url'][] = 'regex:#^https://(www\.)?google\.[a-z.]+/maps/embed#i';
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        return collect(self::FIELDS)->flatMap(fn ($f, $key) => ["values.{$key}" => strtolower($f[0]), "uploads.{$key}" => strtolower($f[0])])->all();
    }

    protected function messages(): array
    {
        return [
            'values.map_embed_url.regex' => 'Paste the embed address that starts with https://www.google.com/maps/embed.',
            'values.google_analytics_id.regex' => 'Enter a measurement ID such as G-XXXXXXXXXX.',
        ];
    }

    public function updatedTab(): void
    {
        $this->resetValidation();
        $this->reset('uploads', 'remove');
    }

    public function removeImage(string $key): void
    {
        unset($this->uploads[$key]);
        $this->remove[$key] = true;
    }

    public function save(SettingsService $settings, ImageService $images, ActivityLogger $logger): void
    {
        $this->validate();

        $data = [];

        foreach ($this->tabDefinitions() as $key => $definition) {
            if ($definition['type'] !== 'image') {
                $value = $this->values[$key] ?? null;
                $data[$key] = is_string($value) ? trim($value) : $value;

                continue;
            }

            if (! empty($this->uploads[$key])) {
                $old = $settings->get($key);
                $data[$key] = $images->storeSingle($this->uploads[$key], 'brand', $key === 'og_image' ? 1200 : 600);
                if ($old && ! str_starts_with($old, 'brand/logo-') && $old !== 'brand/og-image.jpg') {
                    $images->delete($old);
                }
            } elseif (! empty($this->remove[$key])) {
                $data[$key] = null;
            }
        }

        $settings->set($data);
        $logger->log('updated', 'Updated site settings ('.(self::TABS[$this->tab][0] ?? $this->tab).')');

        $this->reset('uploads', 'remove');
        $this->toast('Settings saved.');
    }

    public function render(SettingsService $settings)
    {
        $fields = collect(config('foundation.settings'))
            ->filter(fn ($d) => $d['group'] === $this->tab)
            ->map(fn ($d, $key) => [
                'key' => $key,
                'type' => $d['type'],
                'label' => self::FIELDS[$key][0] ?? $key,
                'help' => self::FIELDS[$key][1] ?? null,
                'format' => self::FIELDS[$key][2] ?? 'text',
                'current' => $d['type'] === 'image' && empty($this->remove[$key]) ? Media::url($settings->get($key)) : null,
            ]);

        return view('livewire.admin.settings', ['fields' => $fields, 'tabs' => self::TABS]);
    }
}
