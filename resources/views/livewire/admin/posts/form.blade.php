<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.posts.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-navy-900"><x-lucide-arrow-left class="size-4" /> All posts</a>
            <h2 class="mt-1 font-serif text-2xl text-navy-900 sm:text-3xl">{{ $postId ? 'Edit post' : 'New post' }}</h2>
        </div>
        @if ($this->post)
            <div class="flex items-center gap-2">
                @if ($this->post->isScheduled())
                    <x-admin.badge color="sky">Scheduled · {{ $this->post->published_at->format('j M, g:ia') }}</x-admin.badge>
                @elseif ($this->post->isLive())
                    <x-admin.badge color="emerald">Published</x-admin.badge>
                @else
                    <x-admin.badge color="amber">Draft</x-admin.badge>
                @endif
                <x-ui.button variant="secondary" size="sm" :href="route('blog.show', $this->post)" target="_blank">
                    <x-lucide-eye class="size-4" /> {{ $this->post->isLive() ? 'View' : 'Preview' }}
                </x-ui.button>
            </div>
        @endif
    </div>

    <form wire:submit="save" class="grid gap-6 xl:grid-cols-3" novalidate>
        <div class="space-y-6 xl:col-span-2">
            <div class="card space-y-5 p-6">
                <x-ui.input label="Title" model="title" required live />
                <x-ui.field label="URL slug" for="f-slug" error="slug" help="Used in the web address. Leave blank to generate from the title.">
                    <div class="flex rounded-lg shadow-sm">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">/blog/</span>
                        <input id="f-slug" type="text" wire:model="slug" @class(['form-input rounded-l-none shadow-none', 'form-input-error' => $errors->has('slug')])>
                    </div>
                </x-ui.field>
                <x-ui.textarea label="Excerpt" model="excerpt" rows="2" counter="500" help="A short summary shown on listing cards and in search results." />
            </div>

            <div class="card p-6">
                <x-admin.rich-editor model="body" label="Content" placeholder="Tell the story…" />
            </div>

            <div class="card space-y-5 p-6">
                <div>
                    <h3 class="font-semibold text-navy-900">Search engine optimisation</h3>
                    <p class="text-sm text-slate-500">Optional. Defaults to the title and excerpt.</p>
                </div>
                <x-ui.input label="SEO title" model="seo_title" maxlength="70" help="Up to 70 characters." />
                <x-ui.textarea label="SEO description" model="seo_description" rows="2" counter="320" />
            </div>
        </div>

        <aside class="space-y-6">
            <div class="card space-y-4 p-6">
                <h3 class="font-semibold text-navy-900">Publishing</h3>
                <x-ui.field label="Publication date" for="f-published_at" error="published_at"
                    help="Leave empty to publish immediately. Choose a future date to schedule.">
                    <input id="f-published_at" type="datetime-local" wire:model="published_at" class="form-input">
                </x-ui.field>
                <x-ui.toggle label="Featured" model="is_featured" help="Highlighted on the home page and blog." />

                <div class="flex flex-col gap-2 border-t border-slate-100 pt-4">
                    <x-ui.button type="button" variant="gold" wire:click="publish" target="publish" class="w-full">
                        <x-lucide-send class="size-4" /> {{ $currentStatus === 'published' ? 'Update & publish' : 'Publish' }}
                    </x-ui.button>
                    <x-ui.button type="button" variant="secondary" wire:click="saveDraft" target="saveDraft" class="w-full">
                        {{ $currentStatus === 'published' ? 'Unpublish (save as draft)' : 'Save draft' }}
                    </x-ui.button>
                    @if ($postId)
                        <x-ui.button type="submit" target="save" class="w-full">Save changes</x-ui.button>
                    @endif
                </div>
            </div>

            <div class="card space-y-4 p-6">
                <x-ui.select label="Category" model="category_id" :options="$this->categories" placeholder="Uncategorised" />
                <x-ui.input label="Tags" model="tags" help="Separate tags with commas, e.g. scholarships, outreach" />
            </div>

            <div class="card space-y-4 p-6">
                <x-admin.image-upload model="image" :upload="$image" :current="$currentImageUrl" label="Featured image" remove="clearImage" />
                <x-ui.input label="Image description (alt text)" model="featured_image_alt" help="Describe the image for people using screen readers." />
            </div>
        </aside>
    </form>
</div>
