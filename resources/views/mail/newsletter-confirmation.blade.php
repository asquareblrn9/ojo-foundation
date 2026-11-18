<x-mail::message>
# Confirm your subscription

Thank you for your interest in **{{ setting('site_name') }}**. Please confirm that you would like to receive updates about our work.

<x-mail::button :url="$confirmUrl">
Confirm subscription
</x-mail::button>

If you did not request this, you can ignore this email and you will not be subscribed.

With gratitude,<br>
{{ setting('site_name') }}

<small>[Unsubscribe]({{ $unsubscribeUrl }})</small>
</x-mail::message>
