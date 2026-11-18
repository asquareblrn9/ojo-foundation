<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Support\Seo;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function confirm(string $token): View
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->update(['confirmed_at' => $subscriber->confirmed_at ?? now(), 'unsubscribed_at' => null]);

        return $this->message('Subscription confirmed', 'Thank you! You will now receive updates from '.setting('site_name').'.');
    }

    public function unsubscribe(string $token): View
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->update(['unsubscribed_at' => $subscriber->unsubscribed_at ?? now()]);

        return $this->message('You have been unsubscribed', 'You will no longer receive our newsletter. You can subscribe again at any time.');
    }

    private function message(string $title, string $text): View
    {
        $seo = Seo::make($title);
        $seo->noindex = true;

        return view('public.message', compact('title', 'text', 'seo'));
    }
}
