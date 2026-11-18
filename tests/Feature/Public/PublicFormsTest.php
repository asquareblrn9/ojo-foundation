<?php

use App\Livewire\Public\ContactForm;
use App\Livewire\Public\NewsletterForm;
use App\Livewire\Public\PartnershipForm;
use App\Livewire\Public\VolunteerForm;
use App\Mail\NewsletterConfirmation;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\PartnershipEnquiry;
use App\Models\VolunteerApplication;
use Database\Seeders\SettingsSeeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(SettingsSeeder::class);
    RateLimiter::clear('contact-form:127.0.0.1');
});

function filledContactForm()
{
    $component = Livewire::test(ContactForm::class)
        ->set('name', 'Ada Lovelace')
        ->set('email', 'ada@example.com')
        ->set('phone', '+1 414 555 0100')
        ->set('subject', 'Partnership')
        ->set('message', 'I would love to support your scholarship programme.');

    test()->travel(5)->seconds();

    return $component;
}

it('stores a valid contact message', function () {
    filledContactForm()->call('submit')->assertHasNoErrors()->assertSet('submitted', true);

    $message = ContactMessage::sole();
    expect($message->email)->toBe('ada@example.com')
        ->and($message->read_at)->toBeNull()
        ->and($message->ip_address)->toBe('127.0.0.1');
});

it('validates the contact form', function () {
    Livewire::test(ContactForm::class)
        ->set('email', 'not-an-email')
        ->set('phone', 'call me <script>')
        ->call('submit')
        ->assertHasErrors(['name' => 'required', 'email', 'phone', 'subject' => 'required', 'message' => 'required']);

    expect(ContactMessage::count())->toBe(0);
});

it('silently drops submissions that fill the honeypot', function () {
    filledContactForm()->set('website_url', 'http://spam.example')->call('submit')->assertSet('submitted', true);

    expect(ContactMessage::count())->toBe(0);
});

it('silently drops submissions made too quickly', function () {
    Livewire::test(ContactForm::class)
        ->set('name', 'Bot')->set('email', 'bot@example.com')->set('subject', 'Hi')
        ->set('message', 'This was submitted instantly.')
        ->call('submit')
        ->assertSet('submitted', true);

    expect(ContactMessage::count())->toBe(0);
});

it('rate limits contact submissions', function () {
    foreach (range(1, 5) as $_) {
        filledContactForm()->call('submit');
    }

    filledContactForm()->call('submit')->assertHasErrors('form');
    expect(ContactMessage::count())->toBe(5);
});

it('stores a volunteer application', function () {
    $component = Livewire::test(VolunteerForm::class, ['areas' => ['Skills Acquisition']])
        ->set('full_name', 'Grace Hopper')
        ->set('email', 'grace@example.com')
        ->set('phone', '+1 414 555 0101')
        ->set('area_of_interest', 'Skills Acquisition')
        ->set('availability', 'Weekends')
        ->set('motivation', 'I want to teach computer literacy to young people.')
        ->set('consent', true);

    $this->travel(5)->seconds();
    $component->call('submit')->assertHasNoErrors()->assertSet('submitted', true);

    expect(VolunteerApplication::sole())
        ->full_name->toBe('Grace Hopper')
        ->status->value->toBe('pending');
});

it('requires consent and a valid area for volunteer applications', function () {
    Livewire::test(VolunteerForm::class)
        ->set('area_of_interest', 'Not a real area')
        ->call('submit')
        ->assertHasErrors(['consent' => 'accepted', 'area_of_interest', 'full_name', 'motivation']);
});

it('stores a partnership enquiry', function () {
    $component = Livewire::test(PartnershipForm::class)
        ->set('organisation', 'Grace Church')
        ->set('contact_name', 'John Doe')
        ->set('email', 'john@example.org')
        ->set('website', 'https://example.org')
        ->set('partnership_type', 'Child sponsorship')
        ->set('message', 'Our congregation would like to sponsor children.');

    $this->travel(5)->seconds();
    $component->call('submit')->assertHasNoErrors();

    expect(PartnershipEnquiry::sole()->organisation)->toBe('Grace Church');
});

it('rejects unsafe partnership website URLs', function () {
    Livewire::test(PartnershipForm::class)->set('website', 'javascript:alert(1)')->call('submit')->assertHasErrors('website');
});

it('subscribes to the newsletter with double opt-in', function () {
    Mail::fake();

    $component = Livewire::test(NewsletterForm::class)->set('email', 'Reader@Example.com');
    $this->travel(5)->seconds();
    $component->call('subscribe')->assertHasNoErrors()->assertSee('check your inbox');

    $subscriber = NewsletterSubscriber::sole();
    expect($subscriber->email)->toBe('reader@example.com')->and($subscriber->confirmed_at)->toBeNull();
    Mail::assertQueued(NewsletterConfirmation::class);

    $this->get(route('newsletter.confirm', $subscriber->token))->assertOk()->assertSee('Subscription confirmed');
    expect($subscriber->fresh()->confirmed_at)->not->toBeNull();

    $this->get(route('newsletter.unsubscribe', $subscriber->token))->assertOk();
    expect($subscriber->fresh()->unsubscribed_at)->not->toBeNull();
});

it('does not reveal whether an email is already subscribed', function () {
    Mail::fake();
    NewsletterSubscriber::factory()->create(['email' => 'known@example.com']);

    $component = Livewire::test(NewsletterForm::class)->set('email', 'known@example.com');
    $this->travel(5)->seconds();
    $component->call('subscribe')->assertSee('check your inbox');

    expect(NewsletterSubscriber::count())->toBe(1);
    Mail::assertNothingQueued();
});

it('returns 404 for an invalid newsletter token', function () {
    $this->get(route('newsletter.confirm', 'invalid-token'))->assertNotFound();
});
