<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Set Your Password') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f5f7; margin: 0; }
        .wrap { max-width: 420px; margin: 8vh auto; background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
        h1 { font-size: 1.25rem; margin: 0 0 1.5rem; }
        label { display: block; font-size: .875rem; margin: 1rem 0 .35rem; }
        input { width: 100%; padding: .6rem .75rem; border: 1px solid #d0d5dd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; margin-top: 1.5rem; padding: .7rem; border: 0; border-radius: 8px; background: #4f46e5; color: #fff; font-size: 1rem; cursor: pointer; }
        .error { background: #fee2e2; color: #991b1b; padding: .6rem .8rem; border-radius: 8px; font-size: .875rem; }
        .field-error { color: #b91c1c; font-size: .8rem; margin-top: .3rem; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>{{ __('Set Your Password') }}</h1>

        @if (session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('accept-invitation.store', $code) }}" method="POST">
            @csrf

            <label for="password">{{ __('Password') }}</label>
            <input type="password" name="password" id="password" required autofocus>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>

            <button type="submit">{{ __('Activate Account') }}</button>
        </form>
    </div>
</body>
</html>
