<?php

namespace App\Livewire\Admin\Inbox;

use App\Models\VolunteerApplication;

class VolunteerShow extends ApplicationShow
{
    public function mount(VolunteerApplication $application): void
    {
        $this->load($application);
    }

    protected function model(): string
    {
        return VolunteerApplication::class;
    }

    protected function config(): array
    {
        return [
            'title' => 'Volunteer application',
            'indexRoute' => 'admin.volunteers.index',
            'nameField' => 'full_name',
            'fields' => [
                'email' => 'Email', 'phone' => 'Phone', 'location' => 'Location', 'area_of_interest' => 'Area of interest',
                'availability' => 'Availability', 'skills' => 'Skills & experience', 'motivation' => 'Motivation',
            ],
        ];
    }
}
