<?php

namespace App\Livewire\Admin\Gallery;

use App\Enums\Permission;
use App\Models\GalleryCategory;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Gallery categories power the filter buttons on the public Gallery page. */
#[Layout('layouts.admin')]
#[Title('Gallery categories')]
class Categories extends Component
{
    use RequiresPermission, WithToasts;

    #[Locked]
    public ?int $editingId = null;

    public string $name = '';

    protected function permission(): Permission
    {
        return Permission::ManageGallery;
    }

    public function edit(int $id): void
    {
        $category = GalleryCategory::findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
    }

    public function cancel(): void
    {
        $this->reset('editingId', 'name');
        $this->resetValidation();
    }

    public function save(): void
    {
        $data = $this->validate(['name' => ['required', 'string', 'max:100', Rule::unique('gallery_categories', 'name')->ignore($this->editingId)]]);

        if ($this->editingId) {
            GalleryCategory::findOrFail($this->editingId)->update($data);
        } else {
            GalleryCategory::create($data + ['sort_order' => (int) GalleryCategory::max('sort_order') + 1]);
        }

        $this->toast('Category saved.');
        $this->cancel();
    }

    public function reorder(int $id, int $position): void
    {
        $ids = GalleryCategory::orderBy('sort_order')->pluck('id')->reject(fn ($i) => $i === $id)->values();
        $ids->splice($position, 0, [$id]);
        $ids->each(fn ($categoryId, $index) => GalleryCategory::whereKey($categoryId)->update(['sort_order' => $index]));
    }

    public function delete(int $id): void
    {
        GalleryCategory::findOrFail($id)->delete();
        $this->toast('Category deleted. Its photos are now uncategorised.');
    }

    public function render()
    {
        return view('livewire.admin.gallery.categories', [
            'categories' => GalleryCategory::withCount('images')->orderBy('sort_order')->get(),
        ]);
    }
}
