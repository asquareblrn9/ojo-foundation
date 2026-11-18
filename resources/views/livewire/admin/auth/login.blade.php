<div>
    <h2 class="font-serif text-3xl text-navy-900">Welcome back</h2>
    <p class="mt-2 text-sm text-slate-500">Sign in to manage the {{ setting('site_name') }} website.</p>

    @if (session('status'))
        <x-ui.alert type="success" class="mt-6">{{ session('status') }}</x-ui.alert>
    @endif

    <form wire:submit="login" class="mt-8 space-y-5" novalidate>
        <x-ui.input label="Email address" model="email" type="email" autocomplete="username" autofocus required />

        <x-ui.password-input label="Password" model="password" required />

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" wire:model="remember" class="size-4 rounded border-slate-300 text-navy-900 focus:ring-navy-500">
                Remember me
            </label>
            <a href="{{ route('admin.password.request') }}" wire:navigate class="text-sm font-medium text-navy-700 hover:text-navy-900">Forgot password?</a>
        </div>

        <x-ui.button class="w-full" target="login">
            <x-lucide-lock class="size-4" wire:loading.remove wire:target="login" />
            Sign in
        </x-ui.button>
    </form>
</div>
