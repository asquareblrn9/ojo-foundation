{{-- Deliberately self-contained: no database, settings or Vite, so it renders even when those fail. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Something went wrong — {{ config('app.name') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #021431; color: #d6dfee; font-family: Georgia, 'Times New Roman', serif; text-align: center; padding: 1.5rem; }
        h1 { color: #fff; font-size: 2.25rem; margin: .5rem 0 1rem; }
        p { font-family: system-ui, sans-serif; line-height: 1.6; max-width: 32rem; margin: 0 auto; }
        a { display: inline-block; margin-top: 2rem; background: #d0a046; color: #021431; padding: .9rem 1.75rem; border-radius: 999px; font: 600 .9rem system-ui, sans-serif; text-decoration: none; }
        .code { color: #d0a046; font-size: 4rem; font-weight: 600; }
    </style>
</head>
<body>
    <main>
        <div class="code">500</div>
        <h1>Something went wrong</h1>
        <p>We're sorry — an unexpected error occurred. Please try again in a few moments.</p>
        <a href="{{ url('/') }}">Back to home</a>
    </main>
</body>
</html>
