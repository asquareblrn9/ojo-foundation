<div>
    <x-admin.page-header :title="$isEvents ? 'Events & activities' : ($this->currentCategory?->name ?? 'Blog posts')"
        :description="$isEvents ? 'Events and activities are articles in the “Events & Activities” category. They appear in “Recent activities & events” on the home page.' : 'Write, schedule and publish news and stories.'">
        <x-slot:actions>
            <x-ui.button :href="route('admin.posts.create', $category ? ['category' => $category] : [])" wire:navigate>
                <x-lucide-plus class="size-4" /> {{ $isEvents ? 'New event' : 'New post' }}
            </x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        {{-- Filters --}}
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="-mx-1 flex gap-1 overflow-x-auto px-1">
                @foreach (['all' => 'All', 'published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Drafts', 'featured' => 'Featured', 'trashed' => 'Trash'] as $key => $label)
                    <button type="button" wire:click="$set('status', '{{ $key }}')" @class([
                        'filter-chip',
                        'bg-navy-900 text-white' => $status === $key,
                        'text-slate-600 hover:bg-slate-100' => $status !== $key,
                    ])>{{ $label }} <span class="opacity-60">{{ $this->counts[$key] }}</span></button>
                @endforeach
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <select wire:model.live="category" class="form-input sm:w-48" aria-label="Filter by category">
                    <option value="">All categories</option>
                    @foreach ($this->categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <div class="relative sm:w-64">
                    <x-lucide-search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search posts…" class="form-input pl-9" aria-label="Search posts">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto" wire:loading.class="opacity-60">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th class="hidden md:table-cell">Category</th>
                        <th>Status</th>
                        <th class="hidden lg:table-cell">Date</th>
                        <th class="hidden xl:table-cell">Views</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr wire:key="post-{{ $post->id }}">
                            <td class="max-w-md">
                                <div class="flex items-center gap-3">
                                    <div class="size-12 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                        @if ($post->featured_image)
                                            <img src="{{ \App\Support\ResponsiveImage::variant($post->featured_image, 'sm') }}" alt="" class="size-full object-cover" loading="lazy">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        @if ($post->trashed())
                                            <p class="truncate font-medium text-slate-500 line-through">{{ $post->title }}</p>
                                        @else
                                            <a href="{{ route('admin.posts.edit', $post->id) }}" wire:navigate class="block truncate font-medium text-navy-900 hover:text-gold-700">{{ $post->title }}</a>
                                        @endif
                                        <p class="truncate text-xs text-slate-500">
                                            @if ($post->is_featured)<x-lucide-star class="-mt-0.5 inline size-3 fill-gold-400 text-gold-500" /> Featured · @endif
                                            {{ $post->author?->name ?? 'No author' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden md:table-cell">{{ $post->category?->name ?? '—' }}</td>
                            <td>
                                @if ($post->trashed())
                                    <x-admin.badge color="rose">Trashed</x-admin.badge>
                                @elseif ($post->isScheduled())
                                    <x-admin.badge color="sky">Scheduled</x-admin.badge>
                                @elseif ($post->isLive())
                                    <x-admin.badge color="emerald">Published</x-admin.badge>
                                @else
                                    <x-admin.badge color="amber">Draft</x-admin.badge>
                                @endif
                            </td>
                            <td class="hidden whitespace-nowrap text-slate-500 lg:table-cell">
                                {{ ($post->published_at ?? $post->updated_at)->format('j M Y') }}
                            </td>
                            <td class="hidden text-slate-500 xl:table-cell">{{ number_format($post->views) }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    @if ($post->trashed())
                                        <button type="button" wire:click="restore({{ $post->id }})" class="icon-btn" title="Restore"><x-lucide-rotate-ccw class="size-4" /></button>
                                        <button type="button" wire:click="forceDelete({{ $post->id }})" wire:confirm="Permanently delete “{{ $post->title }}”? This cannot be undone." class="icon-btn-danger" title="Delete permanently"><x-lucide-trash-2 class="size-4" /></button>
                                    @else
                                        <button type="button" wire:click="togglePublish({{ $post->id }})" class="icon-btn" title="{{ $post->status->value === 'published' ? 'Unpublish' : 'Publish' }}">
                                            @if ($post->status->value === 'published') <x-lucide-eye-off class="size-4" /> @else <x-lucide-send class="size-4" /> @endif
                                        </button>
                                        <button type="button" wire:click="toggleFeatured({{ $post->id }})" class="icon-btn" title="{{ $post->is_featured ? 'Unfeature' : 'Feature' }}">
                                            <x-lucide-star @class(['size-4', 'fill-gold-400 text-gold-500' => $post->is_featured]) />
                                        </button>
                                        <a href="{{ route('blog.show', $post) }}" target="_blank" class="icon-btn" title="{{ $post->isLive() ? 'View' : 'Preview' }}"><x-lucide-external-link class="size-4" /></a>
                                        <a href="{{ route('admin.posts.edit', $post->id) }}" wire:navigate class="icon-btn" title="Edit"><x-lucide-pencil class="size-4" /></a>
                                        <button type="button" wire:click="delete({{ $post->id }})" wire:confirm="Move “{{ $post->title }}” to the trash?" class="icon-btn-danger" title="Move to trash"><x-lucide-trash-2 class="size-4" /></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <x-lucide-newspaper class="mx-auto size-8 text-slate-300" />
                                <p class="mt-2 text-sm text-slate-500">{{ $search !== '' ? 'No posts match your search.' : 'No posts here yet.' }}</p>
                                @if ($status !== 'trashed')
                                    <a href="{{ route('admin.posts.create', $category ? ['category' => $category] : []) }}" wire:navigate class="mt-4 inline-flex text-sm font-semibold text-navy-700 hover:text-navy-900">Create one →</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($posts->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $posts->links() }}</div>
        @endif
    </div>
</div>
