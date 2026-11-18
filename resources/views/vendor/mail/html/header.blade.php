@props(['url'])
@php
    // every mail carries the company logo from the dashboard settings; the name is the fallback
    $companyLogo = settings()->get('logo_image');
    $companyLogo = $companyLogo ? asset(trim($companyLogo, "'")) : null;
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($companyLogo)
<img src="{{ $companyLogo }}" class="logo" alt="{{ trim(strip_tags($slot)) }}" style="max-height: 75px; width: auto;">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
