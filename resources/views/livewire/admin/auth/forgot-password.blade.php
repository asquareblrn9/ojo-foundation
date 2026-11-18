<div>
    <h2 class="font-serif text-3xl text-navy-900">Forgot your password?</h2>
    <p class="mt-2 text-sm text-slate-500">Enter your account email and we'll send you a secure link to choose a new password.</p>

    @if ($sent)
        <x-ui.alert type="success" class="mt-6">
            If an account exists for <strong>{{ $email }}</strong>, a password reset link is on its way. Please check your inbox.
        </x-ui.alert>
    @else
        <form wire:submit="sendLink" class="mt-8 space-y-5" novalidate>
            <x-ui.input label="Email address" model="email" type="email" autocomplete="email" autofocus required />
            <x-ui.button class="w-full" target="sendLink">Email reset link</x-ui.button>
        </form>
    @endif

    <a href="{{ route('admin.login') }}" wire:navigate class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-navy-700 hover:text-navy-900">
        <x-lucide-arrow-left class="size-4" /> Back to sign in
    </a>
</div>
