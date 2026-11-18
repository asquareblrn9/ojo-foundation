<div class="mx-auto max-w-4xl">
    <x-admin.page-header title="My profile" description="Update your account details and password." />

    <div class="space-y-6">
        <form wire:submit="saveProfile" class="card p-6" novalidate>
            <h3 class="font-semibold text-navy-900">Account details</h3>

            <div class="mt-6 flex items-center gap-5">
                @if ($avatar && method_exists($avatar, 'isPreviewable') && $avatar->isPreviewable())
                    <img src="{{ $avatar->temporaryUrl() }}" alt="" class="size-16 rounded-full object-cover">
                @else
                    <x-admin.avatar :user="auth()->user()" class="size-16 text-lg" />
                @endif
                <div>
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                        <x-lucide-camera class="size-4" /> Change photo
                        <input type="file" wire:model="avatar" accept="image/jpeg,image/png,image/webp" class="sr-only">
                    </label>
                    <p class="form-help">JPG, PNG or WebP, up to 2MB.</p>
                    <p wire:loading wire:target="avatar" class="form-help">Uploading…</p>
                    @error('avatar') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <x-ui.input label="Full name" model="name" required />
                <x-ui.input label="Email address" model="email" type="email" required />
                <x-ui.input label="Job title" model="job_title" class="sm:col-span-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.button target="saveProfile,avatar">Save changes</x-ui.button>
            </div>
        </form>

        <form wire:submit="updatePassword" class="card p-6" novalidate>
            <h3 class="font-semibold text-navy-900">Change password</h3>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <x-ui.password-input label="Current password" model="current_password" class="sm:col-span-2" required />
                <x-ui.password-input label="New password" model="password" autocomplete="new-password" required />
                <x-ui.password-input label="Confirm new password" model="password_confirmation" autocomplete="new-password" required />
            </div>
            <div class="mt-6 flex justify-end">
                <x-ui.button target="updatePassword">Update password</x-ui.button>
            </div>
        </form>
    </div>
</div>
