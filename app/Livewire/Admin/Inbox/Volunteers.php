<?php

namespace App\Livewire\Admin\Inbox;

use App\Models\VolunteerApplication;

class Volunteers extends ApplicationIndex
{
    protected function model(): string
    {
        return VolunteerApplication::class;
    }

    protected function searchable(): array
    {
        return ['full_name', 'email', 'phone', 'location', 'area_of_interest'];
    }

    protected function config(): array
    {
        return [
            'title' => 'Volunteer applications',
            'description' => 'People who applied to volunteer through the Get Involved page.',
            'showRoute' => 'admin.volunteers.show',
            'exportRoute' => 'admin.volunteers.export',
            'nameField' => 'full_name',
            'detailField' => 'area_of_interest',
        ];
    }
}
