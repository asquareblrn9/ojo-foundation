<div>
    <h2 class="font-serif text-3xl text-navy-900">Choose a new password</h2>
    <p class="mt-2 text-sm text-slate-500">Use a strong password you don't use anywhere else.</p>

    <form wire:submit="resetPassword" class="mt-8 space-y-5" novalidate>
        <x-ui.input label="Email address" model="email" type="email" autocomplete="username" required />
        <x-ui.password-input label="New password" model="password" autocomplete="new-password" required />
        <x-ui.password-input label="Confirm new password" model="password_confirmation" autocomplete="new-password" required />
        <x-ui.button class="w-full" target="resetPassword">Reset password</x-ui.button>
    </form>
</div>
