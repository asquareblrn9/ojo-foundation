<div>
    @if ($submitted)
        <x-site.form-success title="Message sent"
            text="Thank you for contacting us. A member of our team will respond as soon as possible."
            action="$set('submitted', false)" actionLabel="Send another message" />
    @else
        <form wire:submit="submit" class="relative grid gap-5 sm:grid-cols-2" novalidate>
            <x-site.honeypot />
            @error('form') <x-ui.alert type="error" class="sm:col-span-2">{{ $message }}</x-ui.alert> @enderror

            <x-site.field label="Your name" model="name" required autocomplete="name" />
            <x-site.field label="Email address" model="email" type="email" required autocomplete="email" />
            <x-site.field label="Phone number" model="phone" type="tel" autocomplete="tel" />
            <x-site.field label="Subject" model="subject" required />
            <x-site.field label="Message" model="message" type="textarea" rows="6" required class="sm:col-span-2" />

            <div class="flex flex-col gap-4 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-500">By sending this message you agree to our <a href="{{ route('privacy') }}" class="underline hover:text-navy-900">privacy policy</a>.</p>
                <x-site.submit>Send message</x-site.submit>
            </div>
        </form>
    @endif
</div>
