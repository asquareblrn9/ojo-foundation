<?php

namespace App\Livewire\Public;

use App\Models\ContactMessage;
use App\Support\Concerns\WithSpamProtection;
use Livewire\Component;

class ContactForm extends Component
{
    use WithSpamProtection;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $subject = '';

    public string $message = '';

    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s().]*$/'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    protected function messages(): array
    {
        return ['phone.regex' => 'Please enter a valid phone number.'];
    }

    public function submit(): void
    {
        $data = $this->validate();

        if ($this->passesSpamChecks('contact-form')) {
            ContactMessage::create($data + [
                'ip_address' => request()->ip(),
                'user_agent' => mb_substr((string) request()->userAgent(), 0, 500),
            ]);
        }

        $this->reset('name', 'email', 'phone', 'subject', 'message');
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.public.contact-form');
    }
}
