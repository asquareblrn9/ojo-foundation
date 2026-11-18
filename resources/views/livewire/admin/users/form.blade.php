<div class="mx-auto max-w-3xl">
    <a href="{{ route('admin.users.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-navy-900"><x-lucide-arrow-left class="size-4" /> All users</a>

    @if ($issuedPassword)
        <div class="card mt-4 p-8 text-center" x-data="{ copied: false }">
            <span class="mx-auto grid size-14 place-items-center rounded-full bg-emerald-100 text-emerald-600"><x-lucide-circle-check class="size-7" /></span>
            <h2 class="mt-4 font-serif text-2xl text-navy-900">Account created for {{ $name }}</h2>
            <p class="mx-auto mt-2 max-w-md text-sm text-slate-600">Share the sign-in details below securely. The temporary password is shown only once; {{ $name }} must choose a new one at first sign-in.</p>
            <dl class="mx-auto mt-6 max-w-sm space-y-2 rounded-xl bg-slate-50 p-4 text-left text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Sign-in page</dt><dd class="font-medium text-navy-900">{{ route('admin.login') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Email</dt><dd class="font-medium text-navy-900">{{ $email }}</dd></div>
                <div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Temporary password</dt><dd><code class="font-mono text-base font-semibold tracking-wider text-navy-900">{{ $issuedPassword }}</code></dd></div>
            </dl>
            <div class="mt-6 flex justify-center gap-2">
                <x-ui.button type="button" variant="secondary" @click="navigator.clipboard.writeText(@js('Sign in at '.route('admin.login').' with '.$email.' and temporary password '.$issuedPassword)); copied = true">
                    <span x-show="!copied">Copy details</span><span x-cloak x-show="copied">Copied</span>
                </x-ui.button>
                <x-ui.button :href="route('admin.users.index')" wire:navigate>Done</x-ui.button>
            </div>
        </div>
    @else
        <form wire:submit="save" class="card mt-4" novalidate>
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="font-semibold text-navy-900">{{ $userId ? 'Edit user' : 'Add user' }}</h2>
                @unless ($userId)<p class="text-sm text-slate-500">A temporary password will be generated. The user must change it at first sign-in.</p>@endunless
            </div>
            <div class="grid gap-5 p-6 sm:grid-cols-2">
                <x-ui.input label="Full name" model="name" required />
                <x-ui.input label="Email address" model="email" type="email" required />
                <x-ui.input label="Job title" model="job_title" />
                <x-ui.select label="Role" model="role" :options="$roles" required :disabled="$isSelf" :help="$isSelf ? 'You cannot change your own role.' : null" />
                @unless ($isSelf)
                    <x-ui.toggle label="Account active" model="is_active" help="Deactivated users cannot sign in." class="sm:col-span-2" />
                @endunless
            </div>
            @error('role') <p class="form-error px-6">{{ $message }}</p> @enderror
            <div class="flex justify-end gap-2 border-t border-slate-100 px-6 py-4">
                <x-ui.button variant="ghost" :href="route('admin.users.index')" wire:navigate>Cancel</x-ui.button>
                <x-ui.button target="save">{{ $userId ? 'Save changes' : 'Create user' }}</x-ui.button>
            </div>
        </form>
    @endif
</div>
