<div>
    <x-admin.page-header title="Albums" description="Group photos by event or activity. Published albums appear on the Gallery page.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.gallery.images')" wire:navigate><x-lucide-images class="size-4" /> All photos</x-ui.button>
            <x-ui.button type="button" wire:click="create"><x-lucide-plus class="size-4" /> New album</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($showForm)
        <form wire:submit="save" class="card mb-6 grid gap-5 p-6 md:grid-cols-2" novalidate>
            <h3 class="font-semibold text-navy-900 md:col-span-2">{{ $editingId ? 'Edit album' : 'New album' }}</h3>
            <x-ui.input label="Title" model="title" required placeholder="e.g. Back-to-school outreach 2026" />
            <x-ui.field label="Event date" for="f-event_date" error="event_date">
                <input id="f-event_date" type="date" wire:model="event_date" class="form-input">
            </x-ui.field>
            <x-ui.select label="Category" model="gallery_category_id" :options="$this->categories" placeholder="No category" />
            <x-ui.toggle label="Published" model="is_published" help="Show this album on the website." class="md:pt-7" />
            <x-ui.textarea label="Description" model="description" rows="3" class="md:col-span-2" />
            <div class="flex gap-2 md:col-span-2">
                <x-ui.button target="save">{{ $editingId ? 'Save album' : 'Create album' }}</x-ui.button>
                <x-ui.button type="button" variant="ghost" wire:click="$set('showForm', false)">Cancel</x-ui.button>
            </div>
        </form>
    @endif

    @if ($albums->isEmpty())
        <div class="card px-6 py-16 text-center">
            <x-lucide-folder-open class="mx-auto size-8 text-slate-300" />
            <p class="mt-2 text-sm text-slate-500">No albums yet. Create one for each event or activity.</p>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($albums as $album)
                @php $cover = $album->coverImage ?? $album->images->first(); @endphp
                <div wire:key="album-{{ $album->id }}" class="card overflow-hidden">
                    <a href="{{ route('admin.gallery.images', ['album' => $album->id]) }}" wire:navigate class="relative block aspect-[16/10] bg-slate-100">
                        @if ($cover)
                            <img src="{{ $cover->url('md') }}" alt="" class="size-full object-cover" loading="lazy">
                        @else
                            <div class="grid size-full place-items-center text-slate-400"><x-lucide-images class="size-8" /></div>
                        @endif
                        <span class="absolute top-3 left-3"><x-admin.badge :color="$album->is_published ? 'emerald' : 'amber'">{{ $album->is_published ? 'Published' : 'Hidden' }}</x-admin.badge></span>
                    </a>
                    <div class="p-4">
                        <h3 class="font-semibold text-navy-900">{{ $album->title }}</h3>
                        <p class="text-sm text-slate-500">
                            {{ $album->images_count }} {{ Str::plural('photo', $album->images_count) }}
                            @if ($album->event_date) · {{ $album->event_date->format('j M Y') }} @endif
                            @if ($album->category) · {{ $album->category->name }} @endif
                        </p>
                        <div class="mt-3 flex items-center gap-1 border-t border-slate-100 pt-3">
                            <a href="{{ route('admin.gallery.images', ['album' => $album->id]) }}" wire:navigate class="mr-auto inline-flex items-center gap-1.5 text-sm font-semibold text-navy-700 hover:text-navy-900"><x-lucide-upload class="size-4" /> Manage photos</a>
                            <button type="button" wire:click="togglePublish({{ $album->id }})" class="icon-btn" title="{{ $album->is_published ? 'Hide' : 'Publish' }}">
                                @if ($album->is_published) <x-lucide-eye-off class="size-4" /> @else <x-lucide-eye class="size-4" /> @endif
                            </button>
                            @if ($album->is_published && $album->images_count)
                                <a href="{{ route('gallery.album', $album) }}" target="_blank" class="icon-btn" title="View on website"><x-lucide-external-link class="size-4" /></a>
                            @endif
                            <button type="button" wire:click="edit({{ $album->id }})" class="icon-btn" title="Edit"><x-lucide-pencil class="size-4" /></button>
                            <button type="button" wire:click="delete({{ $album->id }})" wire:confirm="Delete the “{{ $album->title }}” album? Its photos will stay in the gallery." class="icon-btn-danger" title="Delete"><x-lucide-trash-2 class="size-4" /></button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
