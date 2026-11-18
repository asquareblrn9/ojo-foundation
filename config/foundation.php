<?php

/*
|--------------------------------------------------------------------------
| Fola Ojo Foundation application configuration
|--------------------------------------------------------------------------
| `settings` declares every CMS-editable site setting: its group, type,
| and default. The SettingsSeeder writes these to the database and the
| admin Settings module edits them. Values marked as placeholders should
| be replaced by the foundation through the admin dashboard.
*/

return [

    'admin_notification_email' => env('ADMIN_NOTIFICATION_EMAIL'),

    // Disk for uploaded media: "public" (local) or "s3" (production).
    'media_disk' => env('MEDIA_DISK', 'public'),

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    'uploads' => [
        'image_mimes' => ['jpg', 'jpeg', 'png', 'webp'],
        'image_max_kb' => 5120,
        'variant_widths' => ['sm' => 400, 'md' => 800, 'lg' => 1600],
    ],

    'settings' => [
        // General
        'site_name' => ['group' => 'general', 'type' => 'string', 'default' => 'Fola Ojo Foundation'],
        'site_short_name' => ['group' => 'general', 'type' => 'string', 'default' => 'FOF'],
        'tagline' => ['group' => 'general', 'type' => 'string', 'default' => 'A Helping Hand'],
        'logo' => ['group' => 'general', 'type' => 'image', 'default' => null],
        'logo_light' => ['group' => 'general', 'type' => 'image', 'default' => null],
        'favicon' => ['group' => 'general', 'type' => 'image', 'default' => null],

        // Contact
        'contact_email' => ['group' => 'contact', 'type' => 'string', 'default' => null],
        'contact_phone' => ['group' => 'contact', 'type' => 'string', 'default' => null],
        'contact_phone_alt' => ['group' => 'contact', 'type' => 'string', 'default' => null],
        'address' => ['group' => 'contact', 'type' => 'text', 'default' => 'Milwaukee, Wisconsin, USA'],
        'office_hours' => ['group' => 'contact', 'type' => 'string', 'default' => null],
        'map_embed_url' => ['group' => 'contact', 'type' => 'string', 'default' => null],

        // Social
        'social_facebook' => ['group' => 'social', 'type' => 'string', 'default' => null],
        'social_instagram' => ['group' => 'social', 'type' => 'string', 'default' => null],
        'social_x' => ['group' => 'social', 'type' => 'string', 'default' => null],
        'social_linkedin' => ['group' => 'social', 'type' => 'string', 'default' => null],
        'social_youtube' => ['group' => 'social', 'type' => 'string', 'default' => null],
        'social_tiktok' => ['group' => 'social', 'type' => 'string', 'default' => null],

        // SEO
        'seo_title' => ['group' => 'seo', 'type' => 'string', 'default' => 'Fola Ojo Foundation — A Helping Hand'],
        'seo_description' => ['group' => 'seo', 'type' => 'text', 'default' => 'Fola Ojo Foundation Inc is a faith-based nonprofit raising a generation of educated, skilled and self-reliant young people through scholarships, mentorship and skills acquisition.'],
        'seo_keywords' => ['group' => 'seo', 'type' => 'string', 'default' => 'Fola Ojo Foundation, FOF, A Helping Hand, nonprofit, scholarships, education, mentorship, skills acquisition, youth empowerment, Milwaukee'],
        'og_image' => ['group' => 'seo', 'type' => 'image', 'default' => null],
        'google_analytics_id' => ['group' => 'seo', 'type' => 'string', 'default' => null],

        // Footer
        'footer_about' => ['group' => 'footer', 'type' => 'text', 'default' => 'A faith-based nonprofit committed to raising a generation of educated, skilled and self-reliant young people — because no child should be too poor to learn.'],
        'footer_copyright' => ['group' => 'footer', 'type' => 'string', 'default' => 'Fola Ojo Foundation Inc. All rights reserved.'],

        // Donations
        'donations_enabled' => ['group' => 'donations', 'type' => 'bool', 'default' => false],
        'donation_heading' => ['group' => 'donations', 'type' => 'string', 'default' => 'Support our work'],
        'donation_text' => ['group' => 'donations', 'type' => 'text', 'default' => 'A scholarship can open a classroom door. A textbook can awaken curiosity. A school uniform can restore dignity.'],
        'donation_url' => ['group' => 'donations', 'type' => 'string', 'default' => null],
        'donation_bank_details' => ['group' => 'donations', 'type' => 'text', 'default' => null],

        // Newsletter
        'newsletter_enabled' => ['group' => 'newsletter', 'type' => 'bool', 'default' => true],
        'newsletter_double_opt_in' => ['group' => 'newsletter', 'type' => 'bool', 'default' => true],
        'newsletter_heading' => ['group' => 'newsletter', 'type' => 'string', 'default' => 'Stay connected'],
        'newsletter_text' => ['group' => 'newsletter', 'type' => 'text', 'default' => 'Be the first to hear how you can partner with us as we launch our first official empowerment project.'],

        // Notifications
        'notification_email' => ['group' => 'notifications', 'type' => 'string', 'default' => null],
        'notify_contact' => ['group' => 'notifications', 'type' => 'bool', 'default' => true],
        'notify_volunteer' => ['group' => 'notifications', 'type' => 'bool', 'default' => true],
        'notify_partnership' => ['group' => 'notifications', 'type' => 'bool', 'default' => true],
        'notify_newsletter' => ['group' => 'notifications', 'type' => 'bool', 'default' => false],

        // Security
        'turnstile_enabled' => ['group' => 'security', 'type' => 'bool', 'default' => false],
    ],
];
