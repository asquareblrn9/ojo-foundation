<?php

namespace App\Livewire\Admin\Pages;

use App\Enums\Permission;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\ImageService;
use App\Support\Concerns\HandlesImageUpload;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithEditorUploads;
use App\Support\Concerns\WithToasts;
use App\Support\ResponsiveImage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Stevebauman\Purify\Facades\Purify;

/**
 * Generic page editor. Every section's fields are derived from the content
 * stored for it (text fields, plus an optional repeatable "items" list),
 * so all CMS sections are editable without a hand-written form per page.
 */
#[Layout('layouts.admin')]
class Edit extends Component
{
    use HandlesImageUpload, RequiresPermission, WithEditorUploads, WithFileUploads, WithToasts;

    /** Content keys that hold paragraphs of text. */
    private const LONG_FIELDS = ['body', 'text', 'quote', 'vision_text', 'mission_text', 'description'];

    /** Pages whose main body is edited as rich text. */
    private const BODY_PAGES = ['privacy-policy', 'terms'];

    #[Locked]
    public int $pageId;

    public string $title = '';

    public string $seo_title = '';

    public string $seo_description = '';

    public bool $is_published = true;

    public string $body = '';

    /** @var array<int, array{title: ?string, subtitle: ?string, is_active: bool, content: array}> */
    public array $sections = [];

    /** @var array<int, mixed> New image uploads keyed by section id. */
    public array $sectionImages = [];

    /** @var array<int, bool> Sections whose image should be removed. */
    public array $removeImages = [];

    public function mount(Page $page): void
    {
        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->seo_title = (string) $page->seo_title;
        $this->seo_description = (string) $page->seo_description;
        $this->is_published = $page->is_published;
        $this->body = (string) $page->body;

        foreach ($page->sections as $section) {
            $this->sections[$section->id] = [
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'is_active' => $section->is_active,
                'content' => $section->content ?? [],
            ];
        }
    }

    protected function permission(): Permission
    {
        return Permission::ManagePages;
    }

    public static function isLongField(string $key, mixed $value): bool
    {
        return in_array($key, self::LONG_FIELDS, true) || str_ends_with($key, '_text') || (is_string($value) && mb_strlen($value) > 120);
    }

    public static function label(string $key): string
    {
        return Str::of($key)->replace(['_url', '_'], [' link', ' '])->ucfirst()->toString();
    }

    /* ------------------------------ Items ------------------------------ */

    public function addItem(int $sectionId): void
    {
        $items = $this->sections[$sectionId]['content']['items'] ?? [];
        $template = array_fill_keys(array_keys($items[0] ?? ['title' => '', 'text' => '']), '');
        $this->sections[$sectionId]['content']['items'][] = $template;
    }

    public function removeItem(int $sectionId, int $index): void
    {
        unset($this->sections[$sectionId]['content']['items'][$index]);
        $this->sections[$sectionId]['content']['items'] = array_values($this->sections[$sectionId]['content']['items']);
    }

    public function moveItem(int $sectionId, int $index, int $direction): void
    {
        $items = $this->sections[$sectionId]['content']['items'] ?? [];
        $target = $index + $direction;

        if (! isset($items[$index], $items[$target])) {
            return;
        }

        [$items[$index], $items[$target]] = [$items[$target], $items[$index]];
        $this->sections[$sectionId]['content']['items'] = $items;
    }

    public function removeSectionImage(int $sectionId): void
    {
        unset($this->sectionImages[$sectionId]);
        $this->removeImages[$sectionId] = true;
    }

    /* ------------------------------ Save ------------------------------- */

    protected function rules(): array
    {
        $linkRule = 'regex:/^(\/(?!\/)|#|https?:\/\/|mailto:|tel:)/i';

        $rules = [
            'title' => ['required', 'string', 'max:150'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'is_published' => ['boolean'],
            'body' => ['nullable', 'string', 'max:200000'],
            'sections' => ['array'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.subtitle' => ['nullable', 'string', 'max:255'],
            'sections.*.is_active' => ['boolean'],
            'sections.*.content.items' => ['sometimes', 'array', 'max:24'],
            'sectionImages.*' => $this->imageRule(),
        ];

        foreach ($this->sections as $id => $section) {
            foreach ($section['content'] as $key => $value) {
                if ($key === 'items') {
                    foreach ($value as $i => $item) {
                        foreach (array_keys($item) as $field) {
                            $rules["sections.{$id}.content.items.{$i}.{$field}"] = ['nullable', 'string', 'max:2000', ...(in_array($field, ['url'], true) ? [$linkRule] : [])];
                        }
                    }

                    continue;
                }

                $rules["sections.{$id}.content.{$key}"] = ['nullable', 'string', 'max:5000', ...(str_ends_with($key, '_url') ? [$linkRule] : [])];
            }
        }

        return $rules;
    }

    protected function messages(): array
    {
        return ['regex' => 'Links must start with / (a page on this site), # or https://.'];
    }

    public function save(ImageService $images): void
    {
        $this->validate();

        $page = Page::findOrFail($this->pageId);
        $page->update([
            'title' => $this->title,
            'seo_title' => $this->seo_title ?: null,
            'seo_description' => $this->seo_description ?: null,
            'is_published' => $page->is_system && in_array($page->slug, ['home'], true) ? true : $this->is_published,
            'body' => in_array($page->slug, self::BODY_PAGES, true) || $page->body !== null
                ? ($this->body !== '' ? Purify::clean($this->body) : null)
                : $page->body,
        ]);

        foreach ($page->sections as $section) {
            $state = $this->sections[$section->id] ?? null;

            if (! $state) {
                continue;
            }

            $content = $this->cleanContent($state['content'] ?? []);
            $data = [
                'title' => $state['title'] ?: null,
                'subtitle' => $state['subtitle'] ?: null,
                'is_active' => (bool) $state['is_active'],
                'content' => $content,
            ];

            if (! empty($this->sectionImages[$section->id])) {
                $images->deleteWithVariants($section->image);
                $data['image'] = $images->store($this->sectionImages[$section->id], 'pages')['path'];
            } elseif (! empty($this->removeImages[$section->id]) && $section->image) {
                $images->deleteWithVariants($section->image);
                $data['image'] = null;
            }

            $section->update($data);
        }

        Page::forgetCache($page->slug);
        $this->reset('sectionImages', 'removeImages');
        $this->toast('Page saved. Changes are live on the website.');
    }

    /** Trim strings and strip any markup from plain-text section content. */
    private function cleanContent(array $content): array
    {
        return collect($content)->map(function ($value, $key) {
            if ($key === 'items' && is_array($value)) {
                return array_values(array_map(fn ($item) => array_map(fn ($v) => trim(strip_tags((string) $v)), $item), $value));
            }

            return is_string($value) ? trim(strip_tags($value)) : $value;
        })->all();
    }

    public function render()
    {
        $page = Page::with('sections')->findOrFail($this->pageId);

        return view('livewire.admin.pages.edit', [
            'page' => $page,
            'models' => $page->sections->keyBy('id'),
            'hasBody' => in_array($page->slug, self::BODY_PAGES, true) || $page->body !== null,
            'imageUrls' => $page->sections->mapWithKeys(fn (PageSection $s) => [$s->id => empty($this->removeImages[$s->id]) ? ResponsiveImage::variant($s->image, 'md') : null]),
            'icons' => \App\Livewire\Admin\Programmes\Form::ICONS + [
                'eye' => 'Eye', 'target' => 'Target', 'scale' => 'Scales', 'wallet' => 'Wallet', 'sparkles' => 'Sparkles',
                'sunrise' => 'Sunrise', 'hand-coins' => 'Coins', 'handshake' => 'Handshake', 'star' => 'Star',
            ],
        ])->title('Edit page: '.$page->title);
    }
}
