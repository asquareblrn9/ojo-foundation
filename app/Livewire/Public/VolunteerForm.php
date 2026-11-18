<?php

namespace App\Livewire\Public;

use App\Enums\ApplicationStatus;
use App\Models\VolunteerApplication;
use App\Support\Concerns\WithSpamProtection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class VolunteerForm extends Component
{
    use WithSpamProtection;

    public const AVAILABILITY = ['Weekdays', 'Weekends', 'Evenings', 'Flexible', 'Remote only'];

    /** Areas a volunteer can choose from (programme titles plus general roles). */
    #[Locked]
    public array $areas = [];

    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $location = '';

    public string $area_of_interest = '';

    public string $skills = '';

    public string $availability = '';

    public string $motivation = '';

    public bool $consent = false;

    public bool $submitted = false;

    public function mount(array $areas = []): void
    {
        $this->areas = array_values(array_unique([...$areas, 'Mentoring', 'Teaching & tutoring', 'Skills training', 'Fundraising', 'Media & communications', 'Anywhere I am needed']));
    }

    protected function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+\-\s().]*$/'],
            'location' => ['nullable', 'string', 'max:120'],
            'area_of_interest' => ['required', 'string', Rule::in($this->areas)],
            'skills' => ['nullable', 'string', 'max:2000'],
            'availability' => ['required', Rule::in(self::AVAILABILITY)],
            'motivation' => ['required', 'string', 'min:20', 'max:3000'],
            'consent' => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            'consent.accepted' => 'Please confirm that we may contact you about volunteering.',
            'phone.regex' => 'Please enter a valid phone number.',
            'area_of_interest.in' => 'Please choose an area of interest.',
        ];
    }

    public function submit(): void
    {
        $data = $this->validate();
        unset($data['consent']);

        if ($this->passesSpamChecks('volunteer-form', 3)) {
            VolunteerApplication::create($data + [
                'status' => ApplicationStatus::Pending,
                'ip_address' => request()->ip(),
            ]);
        }

        $this->reset('full_name', 'email', 'phone', 'location', 'area_of_interest', 'skills', 'availability', 'motivation', 'consent');
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.public.volunteer-form', ['availabilityOptions' => self::AVAILABILITY]);
    }
}
