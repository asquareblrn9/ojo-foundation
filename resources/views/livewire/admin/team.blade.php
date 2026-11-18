<div>
    <x-admin.page-header title="Team" description="Leadership shown on the About page. Drag to reorder.">
        <x-slot:actions>
            <x-ui.button type="button" wire:click="create"><x-lucide-user-plus class="size-4" /> Add member</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($showForm)
        <form wire:submit="save" class="card mb-6 grid gap-6 p-6 md:grid-cols-[14rem_1fr]" novalidate>
            <x-admin.image-upload model="photo" :upload="$photo" :current="$currentPhotoUrl" label="Photo" remove="clearPhoto" aspect="aspect-square" />
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-ui.input label="Full name" model="name" required />
                    <x-ui.input label="Position" model="position" placeholder="e.g. Founder & President" />
                </div>
                <x-ui.textarea label="Short biography" model="bio" rows="4" />
                <x-ui.toggle label="Show on website" model="is_active" />
                <div class="flex gap-2">
                    <x-ui.button target="save,photo">Save</x-ui.button>
                    <x-ui.button type="button" variant="ghost" wire:click="$set('showForm', false)">Cancel</x-ui.button>
                </div>
            </div>
        </form>
    @endif

    <ul wire:sort="reorder" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($members as $member)
            <li wire:key="member-{{ $member->id }}" wire:sort:item="{{ $member->id }}" class="card flex items-center gap-4 p-4">
                <button type="button" wire:sort:handle class="cursor-grab text-slate-300 hover:text-slate-500" aria-label="Drag to reorder"><x-lucide-grip-vertical class="size-4" /></button>
                @if ($member->photo)
                    <img src="{{ \App\Support\ResponsiveImage::variant($member->photo, 'sm') }}" alt="" class="size-14 rounded-full object-cover">
                @else
                    <span class="grid size-14 place-items-center rounded-full bg-navy-900 font-serif font-semibold text-gold-300">{{ $member->initials() }}</span>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold text-navy-900">{{ $member->name }}</p>
                    <p class="truncate text-sm text-slate-500">{{ $member->position }}</p>
                    @unless ($member->is_active)<x-admin.badge color="amber" class="mt-1">Hidden</x-admin.badge>@endunless
                </div>
                <button type="button" wire:click="edit({{ $member->id }})" class="icon-btn" title="Edit"><x-lucide-pencil class="size-4" /></button>
                <button type="button" wire:click="delete({{ $member->id }})" wire:confirm="Remove {{ $member->name }}?" class="icon-btn-danger" title="Remove"><x-lucide-trash-2 class="size-4" /></button>
            </li>
        @empty
            <li class="card col-span-full px-6 py-12 text-center text-sm text-slate-500">No team members yet.</li>
        @endforelse
    </ul>
</div>
