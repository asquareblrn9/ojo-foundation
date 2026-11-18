<div>
    <x-admin.page-header title="Gallery photos" description="Upload, organise and caption photographs. Drag photos to change their order.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.gallery.albums')" wire:navigate><x-lucide-folder-open class="size-4" /> Albums</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.gallery.categories')" wire:navigate><x-lucide-tags class="size-4" /> Categories</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Bulk uploader --}}
    <div class="card mb-6 p-5" x-data="bulkUploader($wire)">
        <div class="grid gap-4 lg:grid-cols-[1fr_16rem_16rem]">
            <label @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="dragging = false; queue($event.dataTransfer.files)"
                   :class="dragging ? 'border-gold-400 bg-gold-50' : 'border-slate-300 bg-slate-50 hover:border-navy-300'"
                   class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-8 text-center transition">
                <x-lucide-cloud-upload class="size-8 text-navy-400" />
                <span class="mt-2 text-sm font-semibold text-navy-900">Drop photos here or click to browse</span>
                <span class="mt-1 text-xs text-slate-500">JPG, PNG or WebP · up to 5MB each · select as many as you like</span>
                <input type="file" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" @change="queue($event.target.files); $event.target.value = ''" :disabled="busy">
            </label>
            <x-ui.select label="Add to album" model="uploadAlbumId" :options="$this->albums" placeholder="No album" />
            <x-ui.select label="Category" model="uploadCategoryId" :options="$this->categories" placeholder="No category" />
        </div>

        <div x-cloak x-show="total > 0" class="mt-4">
            <div class="flex items-center justify-between text-sm">
                <span class="font-medium text-navy-900" x-text="busy ? `Uploading ${done + 1} of ${total}…` : `Finished: ${succeeded} of ${total} uploaded`"></span>
                <button type="button" x-show="!busy" @click="reset()" class="text-slate-500 hover:text-navy-900">Dismiss</button>
            </div>
            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-gold-500 transition-all" :style="`width: ${overall}%`"></div>
            </div>
            <ul x-show="errors.length" class="mt-3 space-y-1 text-sm text-rose-600">
                <template x-for="e in errors" :key="e"><li x-text="e"></li></template>
            </ul>
        </div>
    </div>

    <div class="card">
        {{-- Filters --}}
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 md:flex-row md:items-center">
            <select wire:model.live="album" class="form-input md:w-56" aria-label="Filter by album">
                <option value="">All albums</option>
                <option value="none">Not in an album</option>
                @foreach ($this->albums as $id => $title)<option value="{{ $id }}">{{ $title }}</option>@endforeach
            </select>
            <select wire:model.live="category" class="form-input md:w-56" aria-label="Filter by category">
                <option value="">All categories</option>
                <option value="none">Uncategorised</option>
                @foreach ($this->categories as $id => $name)<option value="{{ $id }}">{{ $name }}</option>@endforeach
            </select>
            <select wire:model.live="visibility" class="form-input md:w-44" aria-label="Filter by visibility">
                <option value="">All photos</option>
                <option value="visible">Visible</option>
                <option value="hidden">Hidden</option>
                <option value="featured">Featured</option>
            </select>
            <p class="text-sm text-slate-500 md:ml-auto">{{ number_format($total) }} {{ Str::plural('photo', $total) }}</p>
        </div>

        {{-- Bulk actions --}}
        @if ($selected)
            <div class="flex flex-wrap items-center gap-2 border-b border-gold-200 bg-gold-50 px-4 py-3 text-sm">
                <span class="font-semibold text-navy-900">{{ count($selected) }} selected</span>
                <button type="button" wire:click="bulk('show')" class="rounded-lg bg-white px-3 py-1.5 ring-1 ring-slate-200 hover:bg-slate-50">Show</button>
                <button type="button" wire:click="bulk('hide')" class="rounded-lg bg-white px-3 py-1.5 ring-1 ring-slate-200 hover:bg-slate-50">Hide</button>
                <button type="button" wire:click="bulk('feature')" class="rounded-lg bg-white px-3 py-1.5 ring-1 ring-slate-200 hover:bg-slate-50">Feature</button>
                <button type="button" wire:click="bulk('unfeature')" class="rounded-lg bg-white px-3 py-1.5 ring-1 ring-slate-200 hover:bg-slate-50">Unfeature</button>
                <span class="flex items-center gap-1">
                    <select wire:model="bulkAlbum" class="rounded-lg border-slate-300 py-1.5 text-sm" aria-label="Move to album">
                        <option value="">Move to album…</option><option value="none">No album</option>
                        @foreach ($this->albums as $id => $title)<option value="{{ $id }}">{{ $title }}</option>@endforeach
                    </select>
                    <button type="button" wire:click="bulk('album')" class="rounded-lg bg-navy-900 px-3 py-1.5 text-white">Move</button>
                </span>
                <span class="flex items-center gap-1">
                    <select wire:model="bulkCategory" class="rounded-lg border-slate-300 py-1.5 text-sm" aria-label="Set category">
                        <option value="">Set category…</option><option value="none">None</option>
                        @foreach ($this->categories as $id => $name)<option value="{{ $id }}">{{ $name }}</option>@endforeach
                    </select>
                    <button type="button" wire:click="bulk('category')" class="rounded-lg bg-navy-900 px-3 py-1.5 text-white">Apply</button>
                </span>
                <button type="button" wire:click="bulk('delete')" wire:confirm="Permanently delete {{ count($selected) }} photo(s)?" class="rounded-lg px-3 py-1.5 text-rose-600 hover:bg-rose-50">Delete</button>
                <button type="button" wire:click="$set('selected', [])" class="ml-auto text-slate-500 hover:text-navy-900">Clear selection</button>
                @error('bulkAlbum') <p class="w-full text-rose-600">{{ $message }}</p> @enderror
                @error('bulkCategory') <p class="w-full text-rose-600">{{ $message }}</p> @enderror
            </div>
        @elseif ($images->isNotEmpty())
            <div class="border-b border-slate-100 px-4 py-2 text-right">
                <button type="button" wire:click="selectAllVisible" class="text-sm font-medium text-navy-700 hover:text-navy-900">Select all shown</button>
            </div>
        @endif

        @if ($images->isEmpty())
            <div class="px-6 py-16 text-center">
                <x-lucide-images class="mx-auto size-8 text-slate-300" />
                <p class="mt-2 text-sm text-slate-500">No photos {{ $album || $category || $visibility ? 'match these filters' : 'yet — upload some above' }}.</p>
            </div>
        @else
            <ul wire:sort="reorder" class="grid grid-cols-2 gap-4 p-4 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6">
                @foreach ($images as $image)
                    <li wire:key="img-{{ $image->id }}" wire:sort:item="{{ $image->id }}"
                        @class(['group relative overflow-hidden rounded-xl bg-white ring-1', 'ring-2 ring-gold-400' => in_array((string) $image->id, $selected, true), 'ring-slate-200' => ! in_array((string) $image->id, $selected, true)])>
                        <div class="relative aspect-square cursor-grab bg-slate-100" wire:sort:handle>
                            <img src="{{ $image->url('sm') }}" alt="{{ $image->alt }}" loading="lazy" @class(['size-full object-cover', 'opacity-40' => ! $image->is_visible])>
                            <label class="absolute top-2 left-2 grid size-7 place-items-center rounded-md bg-white/90 shadow" wire:sort:ignore>
                                <input type="checkbox" wire:model.live="selected" value="{{ $image->id }}" class="size-4 rounded border-slate-300 text-navy-900" aria-label="Select photo">
                            </label>
                            <div class="absolute top-2 right-2 flex gap-1">
                                @if ($image->is_featured)<span class="rounded-md bg-gold-400 p-1 text-navy-950" title="Featured"><x-lucide-star class="size-3.5 fill-current" /></span>@endif
                                @unless ($image->is_visible)<span class="rounded-md bg-slate-800 p-1 text-white" title="Hidden"><x-lucide-eye-off class="size-3.5" /></span>@endunless
                            </div>
                        </div>
                        <div class="p-2.5">
                            <p class="truncate text-sm font-medium text-navy-900" title="{{ $image->caption }}">{{ $image->caption ?: 'No caption' }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $image->album?->title ?? 'No album' }}@if ($image->category) · {{ $image->category->name }}@endif</p>
                            <div class="mt-2 flex justify-between" wire:sort:ignore>
                                <button type="button" wire:click="edit({{ $image->id }})" class="icon-btn" title="Edit"><x-lucide-pencil class="size-4" /></button>
                                <button type="button" wire:click="toggleFeatured({{ $image->id }})" class="icon-btn" title="{{ $image->is_featured ? 'Unfeature' : 'Feature' }}"><x-lucide-star @class(['size-4', 'fill-gold-400 text-gold-500' => $image->is_featured]) /></button>
                                <button type="button" wire:click="toggleVisible({{ $image->id }})" class="icon-btn" title="{{ $image->is_visible ? 'Hide' : 'Show' }}">
                                    @if ($image->is_visible) <x-lucide-eye class="size-4" /> @else <x-lucide-eye-off class="size-4" /> @endif
                                </button>
                                <button type="button" wire:click="delete({{ $image->id }})" wire:confirm="Permanently delete this photo?" class="icon-btn-danger" title="Delete"><x-lucide-trash-2 class="size-4" /></button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            @if ($total > $images->count())
                <div class="border-t border-slate-100 p-4 text-center">
                    <x-ui.button type="button" variant="secondary" wire:click="loadMore" target="loadMore">Load more ({{ $total - $images->count() }} remaining)</x-ui.button>
                </div>
            @endif
        @endif
    </div>

    {{-- Edit modal --}}
    @if ($editing)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-navy-950/60 p-0 backdrop-blur-sm sm:items-center sm:p-4" wire:keydown.escape.window="closeEdit" role="dialog" aria-modal="true" aria-labelledby="edit-photo-title">
            <div class="absolute inset-0" wire:click="closeEdit"></div>
            <form wire:submit="saveEdit" class="relative max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" novalidate>
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 id="edit-photo-title" class="font-semibold text-navy-900">Edit photo</h3>
                    <button type="button" wire:click="closeEdit" class="icon-btn" aria-label="Close"><x-lucide-x class="size-5" /></button>
                </div>
                <div class="grid gap-6 p-6 md:grid-cols-2">
                    <div>
                        <x-admin.image-upload model="replacement" :upload="$replacement" :current="$editing->url('md')" label="Photo" aspect="aspect-[4/3]" help="Upload a new file to replace this photo." />
                    </div>
                    <div class="space-y-4">
                        <x-ui.input label="Caption" model="caption" />
                        <x-ui.textarea label="Description" model="description" rows="3" />
                        <x-ui.input label="Alt text" model="alt" help="Describe the photo for screen readers." />
                        <x-ui.select label="Album" model="editAlbumId" :options="$this->albums" placeholder="No album" />
                        <x-ui.select label="Category" model="editCategoryId" :options="$this->categories" placeholder="No category" />
                        <x-ui.toggle label="Visible on website" model="editVisible" />
                        <x-ui.toggle label="Featured" model="editFeatured" help="Featured photos appear first and on the home page." />
                        @if ($editAlbumId)
                            <x-ui.toggle label="Use as album cover" model="editIsCover" />
                        @endif
                    </div>
                </div>
                <div class="flex justify-end gap-2 border-t border-slate-100 px-6 py-4">
                    <x-ui.button type="button" variant="ghost" wire:click="closeEdit">Cancel</x-ui.button>
                    <x-ui.button target="saveEdit,replacement">Save photo</x-ui.button>
                </div>
            </form>
        </div>
    @endif
</div>
