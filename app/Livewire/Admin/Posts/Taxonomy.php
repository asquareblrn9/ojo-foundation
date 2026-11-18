<?php

namespace App\Livewire\Admin\Posts;

use App\Enums\Permission;
use App\Models\Category;
use App\Models\Tag;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/** Blog categories (create, edit, reorder, delete) and tags (rename, delete). */
#[Layout('layouts.admin')]
#[Title('Categories & tags')]
class Taxonomy extends Component
{
    use RequiresPermission, WithToasts;

    #[Locked]
    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    #[Locked]
    public ?int $editingTagId = null;

    public string $tagName = '';

    protected function permission(): Permission
    {
        return Permission::ManagePosts;
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->description = (string) $category->description;
        $this->resetValidation();
    }

    public function cancel(): void
    {
        $this->reset('editingId', 'name', 'description');
        $this->resetValidation();
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($this->editingId)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            $this->toast('Category updated.');
        } else {
            Category::create($data + ['sort_order' => (int) Category::max('sort_order') + 1]);
            $this->toast('Category created.');
        }

        $this->cancel();
    }

    public function reorder(int $id, int $position): void
    {
        $ids = Category::orderBy('sort_order')->orderBy('name')->pluck('id')->reject(fn ($i) => $i === $id)->values();
        $ids->splice($position, 0, [$id]);
        $ids->each(fn ($categoryId, $index) => Category::whereKey($categoryId)->update(['sort_order' => $index]));
    }

    public function delete(int $id): void
    {
        $category = Category::withCount('posts')->findOrFail($id);
        $category->delete(); // posts.category_id is set to null by the foreign key
        $this->toast($category->posts_count ? "Category deleted. {$category->posts_count} post(s) are now uncategorised." : 'Category deleted.');
    }

    public function editTag(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->editingTagId = $tag->id;
        $this->tagName = $tag->name;
    }

    public function saveTag(): void
    {
        $this->validate(['tagName' => ['required', 'string', 'max:50', Rule::unique('tags', 'name')->ignore($this->editingTagId)]]);
        Tag::findOrFail($this->editingTagId)->update(['name' => $this->tagName]);
        $this->reset('editingTagId', 'tagName');
        $this->toast('Tag renamed.');
    }

    public function deleteTag(int $id): void
    {
        Tag::findOrFail($id)->delete();
        $this->toast('Tag deleted.');
    }

    public function render()
    {
        return view('livewire.admin.posts.taxonomy', [
            'categories' => Category::withCount('posts')->orderBy('sort_order')->orderBy('name')->get(),
            'tags' => Tag::withCount('posts')->orderBy('name')->get(),
        ]);
    }
}
