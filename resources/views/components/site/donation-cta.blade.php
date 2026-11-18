{{-- Donation call-to-action. Renders only when donations are enabled in settings. --}}
@php
    $url = setting('donation_url');
    $bank = setting('donation_bank_details');
@endphp

@if (setting('donations_enabled') && ($url || $bank))
    <section id="donate" {{ $attributes->class('bg-gold-400 py-16 sm:py-20') }}>
        <div class="mx-auto flex max-w-7xl flex-col items-start gap-8 px-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="max-w-2xl" data-reveal>
                <h2 class="font-serif text-3xl font-semibold text-navy-950 sm:text-4xl">{{ setting('donation_heading') }}</h2>
                <p class="mt-3 text-lg text-navy-900/80">{{ setting('donation_text') }}</p>
                @if ($bank)
                    <div class="mt-6 rounded-2xl bg-white/50 p-5 text-sm whitespace-pre-line text-navy-950">{{ $bank }}</div>
                @endif
            </div>
            @if ($url)
                <a href="{{ safe_url($url) }}" target="_blank" rel="noopener"
                   class="inline-flex shrink-0 items-center gap-2 rounded-full bg-navy-950 px-8 py-4 font-semibold text-white shadow-xl transition hover:bg-navy-800" data-reveal>
                    <x-lucide-heart class="size-5 text-gold-300" /> Donate now
                </a>
            @endif
        </div>
    </section>
@endif
