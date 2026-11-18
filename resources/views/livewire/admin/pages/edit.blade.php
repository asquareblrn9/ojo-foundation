@php use App\Livewire\Admin\Pages\Edit; @endphp
<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('admin.pages.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-navy-900"><x-lucide-arrow-left class="size-4" /> All pages</a>
            <h2 class="mt-1 font-serif text-2xl text-navy-900 sm:text-3xl">{{ $page->title }}</h2>
        </div>
        @if (isset(\App\Livewire\Admin\Pages\Index::URLS[$page->slug]))
            <x-ui.button variant="secondary" :href="route(\App\Livewire\Admin\Pages\Index::URLS[$page->slug])" target="_blank"><x-lucide-external-link class="size-4" /> View page</x-ui.button>
        @endif
    </div>

    <form wire:submit="save" class="grid gap-6 xl:grid-cols-3" novalidate>
        <div class="space-y-4 xl:col-span-2">
            @if ($hasBody)
                <div class="card p-6">
                    <x-admin.rich-editor model="body" label="Page content" />
                </div>
            @endif

            @foreach ($sections as $id => $section)
                @php $model = $models[$id]; @endphp
                {{-- wire:ignore.self keeps sections open/closed across Livewire updates --}}
                <details wire:key="section-{{ $id }}" wire:ignore.self class="card group overflow-hidden" @if ($loop->first) open @endif>
                    <summary class="flex cursor-pointer list-none items-center gap-3 px-5 py-4 hover:bg-slate-50">
                        <x-lucide-chevron-right class="size-4 text-slate-400 transition group-open:rotate-90" />
                        <span class="flex-1">
                            <span class="block font-semibold text-navy-900">{{ Edit::label($model->key) }}</span>
                            <span class="block truncate text-xs text-slate-500">{{ $section['title'] ?: ($section['content']['quote'] ?? '—') }}</span>
                        </span>
                        @unless ($section['is_active'])<x-admin.badge color="amber">Hidden</x-admin.badge>@endunless
                    </summary>

                    <div class="space-y-5 border-t border-slate-100 p-5">
                        <x-ui.toggle label="Show this section" model="sections.{{ $id }}.is_active" />

                        <div class="grid gap-5 md:grid-cols-2">
                            <x-ui.input label="Heading" model="sections.{{ $id }}.title" class="md:col-span-2" />
                            <x-ui.input label="Subheading / intro" model="sections.{{ $id }}.subtitle" class="md:col-span-2" />

                            @foreach ($section['content'] as $key => $value)
                                @continue($key === 'items' || is_array($value))
                                @if (Edit::isLongField($key, $value))
                                    <x-ui.textarea :label="Edit::label($key)" model="sections.{{ $id }}.content.{{ $key }}" :rows="$key === 'body' ? 8 : 3" class="md:col-span-2"
                                        :help="$key === 'body' ? 'Leave a blank line between paragraphs.' : null" />
                                @else
                                    <x-ui.input :label="Edit::label($key)" model="sections.{{ $id }}.content.{{ $key }}"
                                        :help="str_ends_with($key, '_url') ? 'A page on this site (e.g. /get-involved) or a full https:// address.' : null" />
                                @endif
                            @endforeach
                        </div>

                        @if (array_key_exists('items', $section['content']))
                            <div>
                                <p class="form-label">Items</p>
                                <div class="space-y-3">
                                    @foreach ($section['content']['items'] as $i => $item)
                                        <div wire:key="item-{{ $id }}-{{ $i }}" class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                                            <div class="mb-3 flex items-center justify-between">
                                                <span class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Item {{ $i + 1 }}</span>
                                                <span class="flex gap-1">
                                                    <button type="button" wire:click="moveItem({{ $id }}, {{ $i }}, -1)" class="icon-btn" title="Move up" @disabled($loop->first)><x-lucide-chevron-up class="size-4" /></button>
                                                    <button type="button" wire:click="moveItem({{ $id }}, {{ $i }}, 1)" class="icon-btn" title="Move down" @disabled($loop->last)><x-lucide-chevron-down class="size-4" /></button>
                                                    <button type="button" wire:click="removeItem({{ $id }}, {{ $i }})" wire:confirm="Remove this item?" class="icon-btn-danger" title="Remove"><x-lucide-trash-2 class="size-4" /></button>
                                                </span>
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2">
                                                @foreach ($item as $field => $fieldValue)
                                                    @if ($field === 'icon')
                                                        <x-ui.select label="Icon" model="sections.{{ $id }}.content.items.{{ $i }}.icon"
                                                            :options="$icons + ($fieldValue && ! isset($icons[$fieldValue]) ? [$fieldValue => $fieldValue] : [])" placeholder="No icon" />
                                                    @elseif (Edit::isLongField($field, $fieldValue))
                                                        <x-ui.textarea :label="Edit::label($field)" model="sections.{{ $id }}.content.items.{{ $i }}.{{ $field }}" rows="2" class="md:col-span-2" />
                                                    @else
                                                        <x-ui.input :label="Edit::label($field)" model="sections.{{ $id }}.content.items.{{ $i }}.{{ $field }}" />
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addItem({{ $id }})" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-navy-700 hover:text-navy-900"><x-lucide-plus class="size-4" /> Add item</button>
                            </div>
                        @endif

                        @if ($model->image || in_array($model->type, ['hero', 'text', 'mission_vision', 'cta', 'founder'], true))
                            <x-admin.image-upload model="sectionImages.{{ $id }}" :upload="$sectionImages[$id] ?? null" :current="$imageUrls[$id]"
                                label="Section image" remove="removeSectionImage({{ $id }})" class="max-w-md" />
                        @endif
                    </div>
                </details>
            @endforeach
        </div>

        <aside class="space-y-6">
            <div class="card space-y-4 p-6 xl:sticky xl:top-24">
                <x-ui.input label="Page title" model="title" required />
                <x-ui.input label="SEO title" model="seo_title" maxlength="70" help="Shown in browser tabs and search results." />
                <x-ui.textarea label="SEO description" model="seo_description" rows="3" counter="320" />
                @unless ($page->slug === 'home')
                    <x-ui.toggle label="Published" model="is_published" />
                @endunless
                <x-ui.button target="save" class="w-full">Save page</x-ui.button>
                @if ($errors->any())
                    <p class="text-sm text-rose-600">Please fix the highlighted fields{{ $sections ? ' (check each section)' : '' }}.</p>
                @endif
            </div>
        </aside>
    </form>
</div>
