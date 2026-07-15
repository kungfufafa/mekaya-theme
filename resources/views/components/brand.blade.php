@if ($brandLogo = mekaya()->getBrandLogo())
    {!! $brandLogo !!}
@elseif (filled($brandPath = config('mekaya.admin.brand')))
    <img {{ $attributes }} src="{{ asset($brandPath) }}" alt="{{ config('app.name') }}" />
@else
    <img
        {{ $attributes }}
        src="{{ asset(mekaya()->assetsPath() . '/mekaya-icon.svg') }}"
        alt="Mekaya"
    />
@endif
