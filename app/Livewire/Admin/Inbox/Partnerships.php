<?php

namespace App\Livewire\Admin\Inbox;

use App\Models\PartnershipEnquiry;

class Partnerships extends ApplicationIndex
{
    protected function model(): string
    {
        return PartnershipEnquiry::class;
    }

    protected function searchable(): array
    {
        return ['organisation', 'contact_name', 'email', 'partnership_type'];
    }

    protected function config(): array
    {
        return [
            'title' => 'Partnership enquiries',
            'description' => 'Organisations and individuals who would like to partner with or support the foundation.',
            'showRoute' => 'admin.partnerships.show',
            'exportRoute' => 'admin.partnerships.export',
            'nameField' => 'organisation',
            'detailField' => 'partnership_type',
        ];
    }
}
