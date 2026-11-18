<?php

namespace App\Livewire\Admin\Gallery;

use App\Enums\Permission;
use App\Models\GalleryAlbum;
use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use App\Services\ActivityLogger;
use App\Services\ImageService;
use App\Support\Concerns\HandlesImageUpload;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Gallery photo manager: bulk upload (one file per request, driven from the
 * browser so large batches never time out), filtering, bulk actions,
 * drag-and-drop ordering and per-photo editing.
 */
#[Layout('layouts.admin')]
#[Title('Gallery photos')]
class Images extends Component
{
    use HandlesImageUpload, RequiresPermission, WithFileUploads, WithToasts;

    private const PAGE = 48;

    #[Url(except: '')]
    public string $album = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: '')]
    public string $visibility = '';

    public int $limit = self::PAGE;

    /** Upload target */
    public ?int $uploadAlbumId = null;

    public ?int $uploadCategoryId = null;

    /** The file currently being uploaded by the bulk uploader. */
    public $photo;

    public array $selected = [];

    public string $bulkAlbum = '';

    public string $bulkCategory = '';

    /** Edit modal */
    #[Locked]
    public ?int $editingId = null;

    public string $caption = '';

    public string $description = '';

    public string $alt = '';

    public ?int $editAlbumId = null;

    public ?int $editCategoryId = null;

    public bool $editFeatured = false;

    public bool $editVisible = true;

    public bool $editIsCover = false;

    public $replacement;

    protected function permission(): Permission
    {
        return Permission::ManageGallery;
    }

    public function mount(): void
    {
        if (ctype_digit($this->album)) {
            $this->uploadAlbumId = (int) $this->album;
            $this->uploadCategoryId = GalleryAlbum::whereKey($this->album)->value('gallery_category_id');
        }
    }

    public function updated($property): void
    {
        if (in_array($property, ['album', 'category', 'visibility'], true)) {
            $this->limit = self::PAGE;
            $this->selected = [];
        }
    }

    #[Computed]
    public function albums(): array
    {
        return GalleryAlbum::orderByDesc('event_date')->orderBy('title')->pluck('title', 'id')->all();
    }

    #[Computed]
    public function categories(): array
    {
        return GalleryCategory::orderBy('sort_order')->pluck('name', 'id')->all();
    }

    private function query(): Builder
    {
        return GalleryImage::query()
            ->when($this->album === 'none', fn ($q) => $q->whereNull('gallery_album_id'))
            ->when(ctype_digit($this->album), fn ($q) => $q->where('gallery_album_id', $this->album))
            ->when($this->category === 'none', fn ($q) => $q->whereNull('gallery_category_id'))
            ->when(ctype_digit($this->category), fn ($q) => $q->where('gallery_category_id', $this->category))
            ->when($this->visibility === 'visible', fn ($q) => $q->where('is_visible', true))
            ->when($this->visibility === 'hidden', fn ($q) => $q->where('is_visible', false))
            ->when($this->visibility === 'featured', fn ($q) => $q->where('is_featured', true))
            ->orderBy('sort_order')->orderByDesc('id');
    }

    /* ----------------------------- Upload ----------------------------- */

    /**
     * Called by the bulk uploader after each file finishes uploading.
     *
     * @return array{ok: bool, error?: string}
     */
    #[Renderless]
    public function storePhoto(ImageService $images): array
    {
        $validator = Validator::make(
            ['photo' => $this->photo, 'album' => $this->uploadAlbumId, 'category' => $this->uploadCategoryId],
            ['photo' => $this->imageRule(true), 'album' => ['nullable', 'exists:gallery_albums,id'], 'category' => ['nullable', 'exists:gallery_categories,id']],
        );

        if ($validator->fails()) {
            $this->reset('photo');

            return ['ok' => false, 'error' => $validator->errors()->first()];
        }

        $stored = $images->store($this->photo, 'gallery');
        $name = pathinfo($this->photo->getClientOriginalName(), PATHINFO_FILENAME);

        GalleryImage::create([
            'gallery_album_id' => $this->uploadAlbumId,
            'gallery_category_id' => $this->uploadCategoryId,
            'path' => $stored['path'],
            'variants' => $stored['variants'],
            'width' => $stored['width'],
            'height' => $stored['height'],
            'alt' => mb_substr(trim(preg_replace('/[_\-]+/', ' ', strip_tags($name))), 0, 255) ?: null,
            'is_visible' => true,
            'sort_order' => (int) GalleryImage::max('sort_order') + 1,
        ]);

        $this->reset('photo');

        return ['ok' => true];
    }

    public function uploadFinished(int $count, ActivityLogger $logger): void
    {
        if ($count > 0) {
            $logger->log('uploaded', "Uploaded {$count} gallery photo(s)");
            $this->toast("{$count} photo(s) uploaded.");
        }
    }

    /* --------------------------- Ordering ---------------------------- */

    public function reorder(int $id, int $position): void
    {
        $ids = $this->query()->limit($this->limit)->pluck('id')->reject(fn ($i) => $i === $id)->values();
        $ids->splice($position, 0, [$id]);

        // Reuse the existing sort positions of the visible set so items
        // outside the current filter keep their place.
        $slots = GalleryImage::whereIn('id', $ids)->orderBy('sort_order')->orderByDesc('id')->pluck('sort_order')->all();
        $slots = count(array_unique($slots)) === count($slots) ? $slots : range(min($slots), min($slots) + count($slots) - 1);

        $ids->each(fn ($imageId, $index) => GalleryImage::whereKey($imageId)->update(['sort_order' => $slots[$index]]));
    }

    /* ---------------------------- Quick ------------------------------ */

    public function toggleVisible(int $id): void
    {
        $image = GalleryImage::findOrFail($id);
        $image->update(['is_visible' => ! $image->is_visible]);
    }

    public function toggleFeatured(int $id): void
    {
        $image = GalleryImage::findOrFail($id);
        $image->update(['is_featured' => ! $image->is_featured]);
    }

    public function delete(int $id, ImageService $images): void
    {
        $this->destroy(GalleryImage::whereKey($id)->get(), $images);
        $this->toast('Photo deleted.');
    }

    /* ----------------------------- Bulk ------------------------------ */

    public function selectAllVisible(): void
    {
        $this->selected = $this->query()->limit($this->limit)->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    public function bulk(string $action, ImageService $images): void
    {
        $ids = array_map('intval', $this->selected);

        if ($ids === []) {
            return;
        }

        $query = GalleryImage::whereIn('id', $ids);

        switch ($action) {
            case 'show':
            case 'hide':
                $query->update(['is_visible' => $action === 'show']);
                break;
            case 'feature':
            case 'unfeature':
                $query->update(['is_featured' => $action === 'feature']);
                break;
            case 'album':
                $this->validate(['bulkAlbum' => ['required']], ['bulkAlbum.required' => 'Choose an album.']);
                $query->update(['gallery_album_id' => $this->bulkAlbum === 'none' ? null : GalleryAlbum::findOrFail($this->bulkAlbum)->id]);
                break;
            case 'category':
                $this->validate(['bulkCategory' => ['required']], ['bulkCategory.required' => 'Choose a category.']);
                $query->update(['gallery_category_id' => $this->bulkCategory === 'none' ? null : GalleryCategory::findOrFail($this->bulkCategory)->id]);
                break;
            case 'delete':
                $this->destroy($query->get(), $images);
                break;
            default:
                return;
        }

        $this->toast(count($ids).' photo(s) updated.');
        $this->reset('selected', 'bulkAlbum', 'bulkCategory');
    }

    private function destroy($photos, ImageService $images): void
    {
        foreach ($photos as $photo) {
            $images->delete([$photo->path, ...array_values($photo->variants ?? [])]);
            $photo->forceDelete();
        }
    }

    /* ----------------------------- Edit ------------------------------ */

    public function edit(int $id): void
    {
        $image = GalleryImage::with('album')->findOrFail($id);
        $this->editingId = $image->id;
        $this->caption = (string) $image->caption;
        $this->description = (string) $image->description;
        $this->alt = (string) $image->alt;
        $this->editAlbumId = $image->gallery_album_id;
        $this->editCategoryId = $image->gallery_category_id;
        $this->editFeatured = $image->is_featured;
        $this->editVisible = $image->is_visible;
        $this->editIsCover = $image->album && $image->album->cover_image_id === $image->id;
        $this->reset('replacement');
        $this->resetValidation();
    }

    public function closeEdit(): void
    {
        $this->reset('editingId', 'replacement');
    }

    public function saveEdit(ImageService $images): void
    {
        $this->validate([
            'caption' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'alt' => ['nullable', 'string', 'max:255'],
            'editAlbumId' => ['nullable', 'exists:gallery_albums,id'],
            'editCategoryId' => ['nullable', 'exists:gallery_categories,id'],
            'replacement' => $this->imageRule(),
        ]);

        $image = GalleryImage::findOrFail($this->editingId);
        $data = [
            'caption' => $this->caption ?: null,
            'description' => $this->description ?: null,
            'alt' => $this->alt ?: null,
            'gallery_album_id' => $this->editAlbumId ?: null,
            'gallery_category_id' => $this->editCategoryId ?: null,
            'is_featured' => $this->editFeatured,
            'is_visible' => $this->editVisible,
        ];

        if ($this->replacement) {
            $old = [$image->path, ...array_values($image->variants ?? [])];
            $stored = $images->store($this->replacement, 'gallery');
            $data += ['path' => $stored['path'], 'variants' => $stored['variants'], 'width' => $stored['width'], 'height' => $stored['height']];
            $images->delete($old);
        }

        $image->update($data);

        if ($image->gallery_album_id) {
            $album = GalleryAlbum::find($image->gallery_album_id);
            if ($this->editIsCover) {
                $album->update(['cover_image_id' => $image->id]);
            } elseif ($album->cover_image_id === $image->id) {
                $album->update(['cover_image_id' => null]);
            }
        }

        $this->closeEdit();
        $this->toast('Photo updated.');
    }

    public function loadMore(): void
    {
        $this->limit += self::PAGE;
    }

    public function render()
    {
        $total = $this->query()->count();

        return view('livewire.admin.gallery.images', [
            'images' => $this->query()->with(['album:id,title', 'category:id,name'])->limit($this->limit)->get(),
            'total' => $total,
            'editing' => $this->editingId ? GalleryImage::find($this->editingId) : null,
        ]);
    }
}
