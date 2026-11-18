<div>
    @if ($submitted)
        <x-site.form-success title="Thank you for reaching out!"
            text="We have received your partnership enquiry and will respond as soon as possible."
            action="$set('submitted', false)" actionLabel="Send another enquiry" />
    @else
        <h3 class="font-serif text-2xl font-semibold text-navy-900">Partnership enquiry</h3>
        <p class="mt-1 text-sm text-slate-500">Individuals are welcome too — simply enter your own name as the organisation.</p>

        <form wire:submit="submit" class="relative mt-8 grid gap-5 sm:grid-cols-2" novalidate>
            <x-site.honeypot />
            @error('form') <x-ui.alert type="error" class="sm:col-span-2">{{ $message }}</x-ui.alert> @enderror

            <x-site.field label="Organisation / church" model="organisation" required autocomplete="organization" />
            <x-site.field label="Contact person" model="contact_name" required autocomplete="name" />
            <x-site.field label="Email address" model="email" type="email" required autocomplete="email" />
            <x-site.field label="Phone number" model="phone" type="tel" autocomplete="tel" />
            <x-site.field label="Website" model="website" type="url" placeholder="https://" autocomplete="url" />
            <x-site.field label="How would you like to partner?" model="partnership_type" type="select" :options="$types" required />
            <x-site.field label="Message" model="message" type="textarea" required class="sm:col-span-2" placeholder="Tell us a little about yourself and how you would like to help." />

            <div class="sm:col-span-2">
                <x-site.submit>Send enquiry</x-site.submit>
            </div>
        </form>
    @endif
</div>
