<?php

namespace App\Livewire\Admin\Gallery;

use App\Enums\Permission;
use App\Models\GalleryAlbum;
use App\Models\GalleryCategory;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Albums group gallery photos by event or activity. */
#[Layout('layouts.admin')]
#[Title('Albums')]
class Albums extends Component
{
    use RequiresPermission, WithToasts;

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    public ?int $gallery_category_id = null;

    public string $event_date = '';

    public bool $is_published = true;

    protected function permission(): Permission
    {
        return Permission::ManageGallery;
    }

    #[Computed]
    public function categories(): array
    {
        return GalleryCategory::orderBy('sort_order')->pluck('name', 'id')->all();
    }

    public function create(): void
    {
        $this->reset('editingId', 'title', 'description', 'gallery_category_id', 'event_date');
        $this->is_published = true;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function edit(int $id): void
    {
        $album = GalleryAlbum::findOrFail($id);
        $this->editingId = $album->id;
        $this->title = $album->title;
        $this->description = (string) $album->description;
        $this->gallery_category_id = $album->gallery_category_id;
        $this->event_date = $album->event_date?->toDateString() ?? '';
        $this->is_published = $album->is_published;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'gallery_category_id' => ['nullable', 'exists:gallery_categories,id'],
            'event_date' => ['nullable', 'date'],
            'is_published' => ['boolean'],
        ]);

        $data['event_date'] = $data['event_date'] ?: null;
        $data['description'] = $data['description'] ?: null;

        if ($this->editingId) {
            GalleryAlbum::findOrFail($this->editingId)->update($data);
            $this->toast('Album updated.');
        } else {
            GalleryAlbum::create($data);
            $this->toast('Album created. Now add photos to it.');
        }

        $this->showForm = false;
    }

    public function togglePublish(int $id): void
    {
        $album = GalleryAlbum::findOrFail($id);
        $album->update(['is_published' => ! $album->is_published]);
        $this->toast($album->is_published ? 'Album published.' : 'Album hidden.');
    }

    public function delete(int $id): void
    {
        $album = GalleryAlbum::withCount('images')->findOrFail($id);
        $album->update(['cover_image_id' => null]);
        $album->images()->update(['gallery_album_id' => null]);
        $album->delete();
        $this->toast($album->images_count ? "Album deleted. Its {$album->images_count} photo(s) were kept in the gallery." : 'Album deleted.');
    }

    public function render()
    {
        return view('livewire.admin.gallery.albums', [
            'albums' => GalleryAlbum::with(['category:id,name', 'coverImage', 'images' => fn ($q) => $q->limit(1)])
                ->withCount('images')
                ->orderByDesc('event_date')->orderByDesc('id')
                ->get(),
        ]);
    }
}
