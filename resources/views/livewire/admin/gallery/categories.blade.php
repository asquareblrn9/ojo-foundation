<div>
    <x-admin.page-header title="Gallery categories" description="Categories are the filter buttons on the public Gallery page. Drag to reorder.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.gallery.images')" wire:navigate><x-lucide-arrow-left class="size-4" /> Back to photos</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card overflow-hidden lg:col-span-2">
            <ul wire:sort="reorder" class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <li wire:key="gc-{{ $category->id }}" wire:sort:item="{{ $category->id }}" class="flex items-center gap-3 bg-white px-5 py-3">
                        <button type="button" wire:sort:handle class="cursor-grab text-slate-300 hover:text-slate-500" aria-label="Drag to reorder"><x-lucide-grip-vertical class="size-4" /></button>
                        <div class="flex-1">
                            <p class="font-medium text-navy-900">{{ $category->name }}</p>
                            <p class="text-xs text-slate-500">{{ $category->images_count }} {{ Str::plural('photo', $category->images_count) }}</p>
                        </div>
                        <a href="{{ route('admin.gallery.images', ['category' => $category->id]) }}" wire:navigate class="icon-btn" title="View photos"><x-lucide-images class="size-4" /></a>
                        <button type="button" wire:click="edit({{ $category->id }})" class="icon-btn" title="Edit"><x-lucide-pencil class="size-4" /></button>
                        <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Delete “{{ $category->name }}”? Photos will be kept but uncategorised." class="icon-btn-danger" title="Delete"><x-lucide-trash-2 class="size-4" /></button>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center text-sm text-slate-500">No categories yet.</li>
                @endforelse
            </ul>
        </div>

        <form wire:submit="save" class="card h-fit space-y-4 p-6" novalidate>
            <h3 class="font-semibold text-navy-900">{{ $editingId ? 'Rename category' : 'New category' }}</h3>
            <x-ui.input label="Name" model="name" required />
            <div class="flex gap-2">
                <x-ui.button target="save">{{ $editingId ? 'Save' : 'Add category' }}</x-ui.button>
                @if ($editingId)<x-ui.button type="button" variant="ghost" wire:click="cancel">Cancel</x-ui.button>@endif
            </div>
        </form>
    </div>
</div>
