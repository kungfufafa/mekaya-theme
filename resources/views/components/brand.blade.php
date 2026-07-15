@php
    $brandLogo = mekaya()->getBrandLogo();
    $brandName = mekaya()->brandName();
@endphp

@if ($brandLogo instanceof \Illuminate\Contracts\Support\Htmlable)
    {!! $brandLogo->toHtml() !!}
@elseif (filled($brandLogo))
    <img {{ $attributes }} src="{{ str_contains($brandLogo, '://') ? $brandLogo : asset($brandLogo) }}" alt="{{ strip_tags((string) $brandName) }}" />
@elseif (filled($brandIcon = mekaya()->brandIconPath()))
    <img {{ $attributes }} src="{{ str_contains($brandIcon, '://') ? $brandIcon : asset($brandIcon) }}" alt="{{ strip_tags((string) $brandName) }}" />
@else
    <span {{ $attributes }}>{{ $brandName }}</span>
@endif
