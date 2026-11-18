<?php

namespace App\Livewire\Public;

use App\Mail\NewsletterConfirmation;
use App\Models\NewsletterSubscriber;
use App\Support\Concerns\WithSpamProtection;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class NewsletterForm extends Component
{
    use WithSpamProtection;

    public string $email = '';

    public string $variant = 'dark';

    public ?string $status = null;

    protected function minimumSeconds(): int
    {
        return 2;
    }

    public function subscribe(): void
    {
        $this->validate(['email' => ['required', 'email:rfc', 'max:190']]);

        if ($this->passesSpamChecks('newsletter', 3)) {
            $this->register(strtolower(trim($this->email)));
        }

        $this->reset('email');
        // Same message whether or not the address already existed (no enumeration).
        $this->status = setting('newsletter_double_opt_in')
            ? 'Almost done! Please check your inbox to confirm your subscription.'
            : 'Thank you for subscribing!';
    }

    private function register(string $email): void
    {
        $doubleOptIn = (bool) setting('newsletter_double_opt_in');
        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $email]);

        if ($subscriber->exists && $subscriber->confirmed_at && ! $subscriber->unsubscribed_at) {
            return;
        }

        $subscriber->fill([
            'ip_address' => request()->ip(),
            'unsubscribed_at' => null,
            'confirmed_at' => $doubleOptIn ? null : now(),
        ])->save();

        if ($doubleOptIn) {
            Mail::to($email)->queue(new NewsletterConfirmation($subscriber));
        }
    }

    public function render()
    {
        return view('livewire.public.newsletter-form');
    }
}
