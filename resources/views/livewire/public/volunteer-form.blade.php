<div>
    @if ($submitted)
        <x-site.form-success title="Thank you for volunteering!"
            text="We have received your application and a member of our team will be in touch soon."
            action="$set('submitted', false)" actionLabel="Submit another application" />
    @else
        <h3 class="font-serif text-2xl font-semibold text-navy-900">Volunteer application</h3>
        <p class="mt-1 text-sm text-slate-500">Fields marked <span class="text-rose-500">*</span> are required.</p>

        <form wire:submit="submit" class="relative mt-8 grid gap-5 sm:grid-cols-2" novalidate>
            <x-site.honeypot />
            @error('form') <x-ui.alert type="error" class="sm:col-span-2">{{ $message }}</x-ui.alert> @enderror

            <x-site.field label="Full name" model="full_name" required autocomplete="name" class="sm:col-span-2" />
            <x-site.field label="Email address" model="email" type="email" required autocomplete="email" />
            <x-site.field label="Phone number" model="phone" type="tel" required autocomplete="tel" />
            <x-site.field label="City / location" model="location" autocomplete="address-level2" />
            <x-site.field label="Availability" model="availability" type="select" :options="$availabilityOptions" required />
            <x-site.field label="Area of interest" model="area_of_interest" type="select" :options="$areas" required class="sm:col-span-2" />
            <x-site.field label="Skills & experience" model="skills" type="textarea" rows="3" placeholder="e.g. teaching, tailoring, IT, counselling…" class="sm:col-span-2" />
            <x-site.field label="Why would you like to volunteer?" model="motivation" type="textarea" required class="sm:col-span-2" />

            <div class="sm:col-span-2">
                <label class="flex items-start gap-3 text-sm text-slate-600">
                    <input type="checkbox" wire:model="consent" class="mt-0.5 size-4 rounded border-slate-300 text-navy-900 focus:ring-navy-500">
                    <span>I agree that {{ setting('site_name') }} may store my details and contact me about volunteering, in line with the <a href="{{ route('privacy') }}" class="font-medium text-navy-700 underline" target="_blank">privacy policy</a>.</span>
                </label>
                @error('consent') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <x-site.submit>Submit application</x-site.submit>
            </div>
        </form>
    @endif
</div>
