<?php

namespace App\Livewire\Admin\Inbox;

use App\Models\PartnershipEnquiry;

class PartnershipShow extends ApplicationShow
{
    public function mount(PartnershipEnquiry $enquiry): void
    {
        $this->load($enquiry);
    }

    protected function model(): string
    {
        return PartnershipEnquiry::class;
    }

    protected function config(): array
    {
        return [
            'title' => 'Partnership enquiry',
            'indexRoute' => 'admin.partnerships.index',
            'nameField' => 'organisation',
            'fields' => [
                'contact_name' => 'Contact person', 'email' => 'Email', 'phone' => 'Phone', 'website' => 'Website',
                'partnership_type' => 'Partnership type', 'message' => 'Message',
            ],
        ];
    }
}
