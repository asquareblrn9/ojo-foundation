<div>
    @if ($status)
        <div class="flex items-start gap-3 rounded-2xl bg-white/10 p-5 text-white" role="status">
            <x-lucide-mail-check class="mt-0.5 size-5 shrink-0 text-gold-300" />
            <p>{{ $status }}</p>
        </div>
    @else
        <form wire:submit="subscribe" class="relative" novalidate>
            <x-site.honeypot />
            <label for="newsletter-email" class="sr-only">Email address</label>
            <div class="flex flex-col gap-3 sm:flex-row">
                <input id="newsletter-email" type="email" wire:model="email" autocomplete="email" placeholder="Your email address"
                       class="w-full rounded-full border-0 bg-white px-6 py-4 text-base text-navy-900 shadow-sm placeholder:text-slate-400 focus:ring-4 focus:ring-gold-400/40 focus:outline-none sm:text-sm"
                       @error('email') aria-invalid="true" aria-describedby="newsletter-error" @enderror>
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-gold-500 px-7 py-4 text-sm font-semibold text-navy-950 transition hover:bg-gold-400 disabled:opacity-60">
                    <span wire:loading.remove wire:target="subscribe">Subscribe</span>
                    <span wire:loading wire:target="subscribe">Subscribing…</span>
                </button>
            </div>
            @error('email') <p id="newsletter-error" class="mt-2 pl-6 text-sm text-rose-300">{{ $message }}</p> @enderror
            @error('form') <p class="mt-2 pl-6 text-sm text-rose-300">{{ $message }}</p> @enderror
            <p class="mt-3 pl-6 text-xs text-navy-300">We respect your privacy. Unsubscribe at any time.</p>
        </form>
    @endif
</div>
