<div>
    <x-admin.page-header title="Categories & tags" description="Organise articles. Drag categories to change their order on the blog." />

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4"><h3 class="font-semibold text-navy-900">Categories</h3></div>
                <ul wire:sort="reorder" class="divide-y divide-slate-100">
                    @foreach ($categories as $category)
                        <li wire:key="cat-{{ $category->id }}" wire:sort:item="{{ $category->id }}" class="flex items-center gap-3 bg-white px-5 py-3">
                            <button type="button" wire:sort:handle class="cursor-grab text-slate-300 hover:text-slate-500" aria-label="Drag to reorder"><x-lucide-grip-vertical class="size-4" /></button>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-navy-900">{{ $category->name }}</p>
                                <p class="truncate text-xs text-slate-500">/blog/category/{{ $category->slug }} · {{ $category->posts_count }} {{ Str::plural('post', $category->posts_count) }}</p>
                            </div>
                            <a href="{{ route('admin.posts.index', ['category' => $category->slug]) }}" wire:navigate class="icon-btn" title="View posts"><x-lucide-newspaper class="size-4" /></a>
                            <button type="button" wire:click="edit({{ $category->id }})" class="icon-btn" title="Edit"><x-lucide-pencil class="size-4" /></button>
                            <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Delete the “{{ $category->name }}” category? Its posts will become uncategorised." class="icon-btn-danger" title="Delete"><x-lucide-trash-2 class="size-4" /></button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card">
                <div class="border-b border-slate-100 px-5 py-4"><h3 class="font-semibold text-navy-900">Tags</h3><p class="text-sm text-slate-500">Tags are created automatically when you add them to a post.</p></div>
                <div class="flex flex-wrap gap-2 p-5">
                    @forelse ($tags as $tag)
                        <span wire:key="tag-{{ $tag->id }}" class="inline-flex items-center gap-1 rounded-full bg-slate-100 py-1 pr-1 pl-3 text-sm text-slate-700">
                            @if ($editingTagId === $tag->id)
                                <form wire:submit="saveTag" class="flex items-center gap-1">
                                    <input type="text" wire:model="tagName" class="w-32 rounded-md border border-slate-300 px-2 py-0.5 text-sm" autofocus>
                                    <button type="submit" class="icon-btn size-6" title="Save"><x-lucide-check class="size-3.5" /></button>
                                </form>
                            @else
                                #{{ $tag->name }} <span class="text-xs text-slate-400">{{ $tag->posts_count }}</span>
                                <button type="button" wire:click="editTag({{ $tag->id }})" class="icon-btn size-6" title="Rename"><x-lucide-pencil class="size-3.5" /></button>
                                <button type="button" wire:click="deleteTag({{ $tag->id }})" wire:confirm="Delete tag “{{ $tag->name }}”?" class="icon-btn-danger size-6" title="Delete"><x-lucide-x class="size-3.5" /></button>
                            @endif
                        </span>
                    @empty
                        <p class="text-sm text-slate-500">No tags yet.</p>
                    @endforelse
                </div>
                @error('tagName') <p class="form-error px-5 pb-4">{{ $message }}</p> @enderror
            </div>
        </div>

        <form wire:submit="save" class="card h-fit space-y-4 p-6" novalidate>
            <h3 class="font-semibold text-navy-900">{{ $editingId ? 'Edit category' : 'New category' }}</h3>
            <x-ui.input label="Name" model="name" required />
            <x-ui.textarea label="Description" model="description" rows="3" />
            <div class="flex gap-2">
                <x-ui.button target="save">{{ $editingId ? 'Save changes' : 'Add category' }}</x-ui.button>
                @if ($editingId)
                    <x-ui.button type="button" variant="ghost" wire:click="cancel">Cancel</x-ui.button>
                @endif
            </div>
        </form>
    </div>
</div>
