{{-- Browser tab icon.
     Uses the configured school logo when it is a PNG/JPG (reliably
     supported as a favicon by all modern browsers); otherwise falls back
     to the static default favicon. --}}
@php $faviconUrl = schoolBrand()->faviconUrl(); @endphp
@if($faviconUrl)
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
@else
    <link rel="icon" href="{{ asset('favicon.ico') }}">
@endif
