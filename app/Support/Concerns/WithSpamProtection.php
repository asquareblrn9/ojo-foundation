<?php

namespace App\Support\Concerns;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

/**
 * Lightweight spam protection for public Livewire forms:
 *  - a honeypot field humans never see or fill,
 *  - a minimum time between rendering and submitting the form,
 *  - per-IP rate limiting.
 *
 * Views must include <x-site.honeypot /> inside the form.
 */
trait WithSpamProtection
{
    /** Honeypot. Bots tend to fill every field. */
    public string $website_url = '';

    #[Locked]
    public int $renderedAt = 0;

    /** Minimum seconds a human needs to fill the form. Override per component. */
    protected function minimumSeconds(): int
    {
        return 3;
    }

    public function mountWithSpamProtection(): void
    {
        $this->renderedAt = now()->getTimestamp();
    }

    /**
     * Returns false when the submission looks automated. Automated submissions
     * are silently accepted (so bots get no signal) but not stored.
     */
    protected function passesSpamChecks(string $limiterName, int $maxAttempts = 5): bool
    {
        $key = $limiterName.':'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            throw ValidationException::withMessages([
                'form' => 'Too many submissions. Please try again in '.RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        RateLimiter::hit($key, 60);

        if ($this->website_url !== '') {
            return false;
        }

        return (now()->getTimestamp() - $this->renderedAt) >= $this->minimumSeconds();
    }
}
