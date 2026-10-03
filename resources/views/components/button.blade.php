@props(['variant' => 'primary', 'href' => null])

@php
$cls = $variant === 'primary' ? 'kka-btn-primary' : 'kka-btn-secondary';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</button>
@endif
