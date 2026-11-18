<div>
    <h2 class="font-serif text-3xl text-navy-900">Set your password</h2>
    <p class="mt-2 text-sm text-slate-500">For security, you must replace the temporary password on this account before continuing.</p>

    <form wire:submit="save" class="mt-8 space-y-5" novalidate>
        <x-ui.password-input label="Current (temporary) password" model="current_password" required />
        <x-ui.password-input label="New password" model="password" autocomplete="new-password" required
            :help="app()->isProduction() ? 'At least 10 characters with upper and lower case letters, a number and a symbol.' : 'At least 8 characters including letters and numbers.'" />
        <x-ui.password-input label="Confirm new password" model="password_confirmation" autocomplete="new-password" required />
        <x-ui.button class="w-full" target="save">Save and continue</x-ui.button>
    </form>

    <form method="POST" action="{{ route('admin.logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-navy-900">Sign out instead</button>
    </form>
</div>
