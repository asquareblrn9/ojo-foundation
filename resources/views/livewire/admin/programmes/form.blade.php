<div>
    <div class="mb-6">
        <a href="{{ route('admin.programmes.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-navy-900"><x-lucide-arrow-left class="size-4" /> All programmes</a>
        <h2 class="mt-1 font-serif text-2xl text-navy-900 sm:text-3xl">{{ $programmeId ? 'Edit programme' : 'New programme' }}</h2>
    </div>

    <form wire:submit="save" class="grid gap-6 xl:grid-cols-3" novalidate>
        <div class="space-y-6 xl:col-span-2">
            <div class="card space-y-5 p-6">
                <x-ui.input label="Title" model="title" required live />
                <x-ui.field label="URL slug" for="f-slug" error="slug">
                    <div class="flex rounded-lg shadow-sm">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">/programmes/</span>
                        <input id="f-slug" type="text" wire:model="slug" @class(['form-input rounded-l-none shadow-none', 'form-input-error' => $errors->has('slug')])>
                    </div>
                </x-ui.field>
                <x-ui.textarea label="Short description" model="short_description" rows="2" counter="400" required help="Shown on programme cards." />
            </div>

            <div class="card p-6">
                <x-admin.rich-editor model="description" label="Full description" placeholder="Describe this programme…" />
            </div>

            <div class="card grid gap-6 p-6 lg:grid-cols-3">
                <x-admin.list-editor model="objectives" label="Objectives" placeholder="Add an objective…" />
                <x-admin.list-editor model="activities" label="Activities" placeholder="Add an activity…" />
                <x-admin.list-editor model="outcomes" label="Impact & outcomes" placeholder="Add an outcome…" help="Only add outcomes you can stand behind." />
            </div>

            <div class="card space-y-5 p-6">
                <h3 class="font-semibold text-navy-900">Search engine optimisation</h3>
                <x-ui.input label="SEO title" model="seo_title" maxlength="70" />
                <x-ui.textarea label="SEO description" model="seo_description" rows="2" counter="320" />
            </div>
        </div>

        <aside class="space-y-6">
            <div class="card space-y-4 p-6">
                <x-ui.toggle label="Published" model="is_published" help="Visible on the website." />
                <x-ui.toggle label="Featured" model="is_featured" help="Shown on the home page." />
                <x-ui.button target="save" class="w-full">Save programme</x-ui.button>
            </div>

            <div class="card p-6">
                <p class="form-label">Icon</p>
                <div class="grid grid-cols-4 gap-2">
                    @foreach ($icons as $name => $iconLabel)
                        <button type="button" wire:click="$set('icon', '{{ $name }}')" title="{{ $iconLabel }}" aria-label="{{ $iconLabel }}"
                                @class(['grid aspect-square place-items-center rounded-xl border transition',
                                    'border-navy-900 bg-navy-900 text-gold-300' => $icon === $name,
                                    'border-slate-200 text-slate-600 hover:border-navy-300' => $icon !== $name])>
                            {{ svg('lucide-'.$name, ['class' => 'size-5']) }}
                        </button>
                    @endforeach
                </div>
                @error('icon') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="card p-6">
                <x-admin.image-upload model="image" :upload="$image" :current="$currentImageUrl" label="Programme image" remove="clearImage" aspect="aspect-[4/3]" />
            </div>
        </aside>
    </form>
</div>
