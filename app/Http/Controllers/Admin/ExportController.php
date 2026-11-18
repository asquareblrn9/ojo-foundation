<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\PartnershipEnquiry;
use App\Models\VolunteerApplication;
use App\Services\ActivityLogger;
use App\Support\CsvExporter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** CSV exports. Access is controlled by route permission middleware. */
class ExportController extends Controller
{
    public function __construct(private ActivityLogger $logger) {}

    public function volunteers(Request $request): StreamedResponse
    {
        $this->logger->log('exported', 'Exported volunteer applications');

        return CsvExporter::download(
            VolunteerApplication::query()->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))->latest(),
            [
                'Name' => 'full_name', 'Email' => 'email', 'Phone' => 'phone', 'Location' => 'location',
                'Area of interest' => 'area_of_interest', 'Availability' => 'availability', 'Skills' => 'skills',
                'Motivation' => 'motivation', 'Status' => 'status', 'Internal notes' => 'internal_notes',
                'Submitted' => fn ($r) => $r->created_at->toDateTimeString(),
            ],
            'volunteer-applications',
        );
    }

    public function partnerships(Request $request): StreamedResponse
    {
        $this->logger->log('exported', 'Exported partnership enquiries');

        return CsvExporter::download(
            PartnershipEnquiry::query()->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))->latest(),
            [
                'Organisation' => 'organisation', 'Contact' => 'contact_name', 'Email' => 'email', 'Phone' => 'phone',
                'Website' => 'website', 'Type' => 'partnership_type', 'Message' => 'message', 'Status' => 'status',
                'Internal notes' => 'internal_notes', 'Submitted' => fn ($r) => $r->created_at->toDateTimeString(),
            ],
            'partnership-enquiries',
        );
    }

    public function messages(): StreamedResponse
    {
        $this->logger->log('exported', 'Exported contact messages');

        return CsvExporter::download(
            ContactMessage::query()->latest(),
            [
                'Name' => 'name', 'Email' => 'email', 'Phone' => 'phone', 'Subject' => 'subject', 'Message' => 'message',
                'Read' => fn ($r) => $r->read_at ? 'Yes' : 'No', 'Resolved' => fn ($r) => $r->resolved_at ? 'Yes' : 'No',
                'Received' => fn ($r) => $r->created_at->toDateTimeString(),
            ],
            'contact-messages',
        );
    }

    public function subscribers(Request $request): StreamedResponse
    {
        $this->logger->log('exported', 'Exported newsletter subscribers');

        $query = match ($request->query('filter', 'active')) {
            'pending' => NewsletterSubscriber::whereNull('confirmed_at')->whereNull('unsubscribed_at'),
            'unsubscribed' => NewsletterSubscriber::whereNotNull('unsubscribed_at'),
            'all' => NewsletterSubscriber::query(),
            default => NewsletterSubscriber::active(),
        };

        return CsvExporter::download($query->latest(), [
            'Email' => 'email', 'Name' => 'name',
            'Status' => fn ($r) => $r->unsubscribed_at ? 'Unsubscribed' : ($r->confirmed_at ? 'Active' : 'Pending'),
            'Subscribed' => fn ($r) => $r->created_at->toDateTimeString(),
        ], 'newsletter-subscribers');
    }
}
