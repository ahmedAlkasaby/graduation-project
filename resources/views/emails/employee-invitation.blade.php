@php
    use Illuminate\Support\Carbon;
@endphp

<x-mail::message>
# {{ __('You have been invited to join :app', ['app' => $appName]) }}

@if ($greetingName)
{{ __('Hello :name,', ['name' => $greetingName]) }}
@else
{{ __('Hello,') }}
@endif

{{ __('An account has been created for you on :app. Use the invitation code below in the app to set your password and activate your account.', ['app' => $appName]) }}

<x-mail::panel>
<div style="text-align:center; font-size:28px; font-weight:700; letter-spacing:8px; font-family:monospace;">
{{ $code }}
</div>
</x-mail::panel>

@if ($expiresAt)
<x-mail::subcopy>
{{ __('This invitation code expires on :date.', ['date' => Carbon::parse($expiresAt)->toDayDateTimeString()]) }}
</x-mail::subcopy>
@endif

<x-mail::button :url="$acceptUrl">
{{ __('Accept invitation') }}
</x-mail::button>

{{ __('Click the button above to set your password, or open the :app app, go to the activation screen, and enter this code to finish setting up your account.', ['app' => $appName]) }}

{{ __('If you were not expecting this invitation, no action is required.') }}

{{ __('Thanks,') }}<br>
{{ $appName }}
</x-mail::message>
