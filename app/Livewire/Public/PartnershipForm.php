<?php

namespace App\Livewire\Public;

use App\Enums\ApplicationStatus;
use App\Models\PartnershipEnquiry;
use App\Support\Concerns\WithSpamProtection;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PartnershipForm extends Component
{
    use WithSpamProtection;

    public const TYPES = ['Child sponsorship', 'Financial donation', 'Educational materials', 'Corporate partnership', 'Church / faith organisation', 'Skills training partner', 'Other'];

    public string $organisation = '';

    public string $contact_name = '';

    public string $email = '';

    public string $phone = '';

    public string $website = '';

    public string $partnership_type = '';

    public string $message = '';

    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'organisation' => ['required', 'string', 'max:190'],
            'contact_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s().]*$/'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'partnership_type' => ['required', Rule::in(self::TYPES)],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number.',
            'partnership_type.in' => 'Please choose how you would like to partner with us.',
        ];
    }

    public function submit(): void
    {
        $data = $this->validate();

        if ($this->passesSpamChecks('partnership-form', 3)) {
            PartnershipEnquiry::create($data + [
                'status' => ApplicationStatus::Pending,
                'ip_address' => request()->ip(),
            ]);
        }

        $this->reset('organisation', 'contact_name', 'email', 'phone', 'website', 'partnership_type', 'message');
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.public.partnership-form', ['types' => self::TYPES]);
    }
}
