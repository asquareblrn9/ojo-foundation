<?php

namespace App\Livewire\Admin\Programmes;

use App\Enums\Permission;
use App\Models\Programme;
use App\Services\ActivityLogger;
use App\Services\ImageService;
use App\Support\Concerns\HandlesImageUpload;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithEditorUploads;
use App\Support\Concerns\WithToasts;
use App\Support\ResponsiveImage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Stevebauman\Purify\Facades\Purify;

#[Layout('layouts.admin')]
class Form extends Component
{
    use HandlesImageUpload, RequiresPermission, WithEditorUploads, WithFileUploads, WithToasts;

    /** Icons offered for programmes (Lucide icon names). */
    public const ICONS = [
        'graduation-cap' => 'Graduation cap', 'book-open' => 'Book', 'compass' => 'Compass', 'wrench' => 'Wrench',
        'house' => 'House', 'heart-handshake' => 'Helping hands', 'hand-heart' => 'Hand & heart', 'users' => 'People',
        'stethoscope' => 'Healthcare', 'sprout' => 'Growth', 'laptop' => 'Computer', 'scissors' => 'Tailoring',
        'utensils' => 'Catering', 'lightbulb' => 'Ideas', 'briefcase' => 'Careers', 'heart' => 'Heart',
    ];

    #[Locked]
    public ?int $programmeId = null;

    public string $title = '';

    public string $slug = '';

    public string $icon = '';

    public string $short_description = '';

    public string $description = '';

    public array $objectives = [];

    public array $activities = [];

    public array $outcomes = [];

    public bool $is_published = true;

    public bool $is_featured = false;

    public string $seo_title = '';

    public string $seo_description = '';

    public $image;

    #[Locked]
    public ?string $currentImage = null;

    public bool $removeImage = false;

    protected function permission(): Permission
    {
        return Permission::ManageProgrammes;
    }

    public function mount(?Programme $programme = null): void
    {
        if (! $programme?->exists) {
            return;
        }

        $this->programmeId = $programme->id;
        $this->title = $programme->title;
        $this->slug = $programme->slug;
        $this->icon = (string) $programme->icon;
        $this->short_description = (string) $programme->short_description;
        $this->description = (string) $programme->description;
        $this->objectives = $programme->objectives ?? [];
        $this->activities = $programme->activities ?? [];
        $this->outcomes = $programme->outcomes ?? [];
        $this->is_published = $programme->is_published;
        $this->is_featured = $programme->is_featured;
        $this->seo_title = (string) $programme->seo_title;
        $this->seo_description = (string) $programme->seo_description;
        $this->currentImage = $programme->featured_image;
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->programmeId && $this->slug === '') {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedImage(): void
    {
        $this->validateOnly('image', ['image' => $this->imageRule()]);
        $this->removeImage = false;
    }

    public function clearImage(): void
    {
        $this->reset('image');
        $this->removeImage = true;
    }

    protected function rules(): array
    {
        $list = ['array', 'max:30'];
        $item = ['string', 'max:500'];

        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:190', 'alpha_dash', Rule::unique('programmes', 'slug')->ignore($this->programmeId)],
            'icon' => ['nullable', Rule::in(array_keys(self::ICONS))],
            'short_description' => ['required', 'string', 'max:400'],
            'description' => ['nullable', 'string', 'max:100000'],
            'objectives' => $list, 'objectives.*' => $item,
            'activities' => $list, 'activities.*' => $item,
            'outcomes' => $list, 'outcomes.*' => $item,
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'image' => $this->imageRule(),
        ];
    }

    public function save(ImageService $images, ActivityLogger $logger): void
    {
        $this->validate();

        $programme = $this->programmeId ? Programme::findOrFail($this->programmeId) : new Programme(['sort_order' => (int) Programme::max('sort_order') + 1]);
        $wasPublished = $programme->exists && $programme->is_published;
        $clean = fn (array $items) => array_values(array_filter(array_map(fn ($i) => trim(strip_tags($i)), $items)));

        $programme->fill([
            'title' => $this->title,
            'slug' => $this->slug !== '' ? $this->slug : null,
            'icon' => $this->icon ?: null,
            'short_description' => $this->short_description,
            'description' => $this->description !== '' ? Purify::clean($this->description) : null,
            'objectives' => $clean($this->objectives),
            'activities' => $clean($this->activities),
            'outcomes' => $clean($this->outcomes),
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'seo_title' => $this->seo_title ?: null,
            'seo_description' => $this->seo_description ?: null,
        ]);

        if ($this->image) {
            $images->deleteWithVariants($programme->featured_image);
            $programme->featured_image = $images->store($this->image, 'programmes')['path'];
        } elseif ($this->removeImage && $programme->featured_image) {
            $images->deleteWithVariants($programme->featured_image);
            $programme->featured_image = null;
        }

        $programme->save();

        if ($programme->is_published !== $wasPublished && ($programme->wasRecentlyCreated ? $programme->is_published : true)) {
            $logger->log($programme->is_published ? 'published' : 'unpublished', ($programme->is_published ? 'Published' : 'Unpublished')." programme \"{$programme->title}\"", $programme);
        }

        $this->flashToast('Programme saved.');
        $this->redirectRoute('admin.programmes.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.programmes.form', [
            'icons' => self::ICONS,
            'currentImageUrl' => $this->removeImage ? null : ResponsiveImage::variant($this->currentImage, 'md'),
        ])->title($this->programmeId ? 'Edit programme' : 'New programme');
    }
}
