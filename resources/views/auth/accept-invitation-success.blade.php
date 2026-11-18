<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Account Ready') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f5f7; margin: 0; }
        .wrap { max-width: 420px; margin: 8vh auto; background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.06); text-align: center; }
        .check { width: 64px; height: 64px; margin: 0 auto 1.25rem; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 2rem; line-height: 1; }
        h1 { font-size: 1.25rem; margin: 0 0 .75rem; }
        p { color: #475467; font-size: .95rem; margin: 0; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="check">&#10003;</div>
        <h1>{{ __('Your account is ready') }}</h1>
        <p>{{ __('You can now sign in from the app.') }}</p>
    </div>
</body>
</html>
