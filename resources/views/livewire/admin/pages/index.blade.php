<div>
    <x-admin.page-header title="Pages" description="Edit the text, images and calls to action on each page of the website." />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($pages as $page)
            <div wire:key="page-{{ $page->id }}" class="card flex flex-col p-5">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid size-11 place-items-center rounded-xl bg-navy-50 text-navy-700"><x-lucide-file-text class="size-5" /></span>
                    <x-admin.badge :color="$page->is_published ? 'emerald' : 'amber'">{{ $page->is_published ? 'Published' : 'Hidden' }}</x-admin.badge>
                </div>
                <h3 class="mt-4 font-semibold text-navy-900">{{ $page->title }}</h3>
                <p class="text-sm text-slate-500">
                    @if ($page->sections_count) {{ $page->sections_count }} editable {{ Str::plural('section', $page->sections_count) }} @else Text page @endif
                    · Updated {{ $page->updated_at->diffForHumans() }}
                </p>
                <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4">
                    <x-ui.button size="sm" :href="route('admin.pages.edit', $page->id)" wire:navigate><x-lucide-pencil class="size-4" /> Edit</x-ui.button>
                    @if (isset($urls[$page->slug]))
                        <x-ui.button size="sm" variant="ghost" :href="route($urls[$page->slug])" target="_blank"><x-lucide-external-link class="size-4" /> View</x-ui.button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
