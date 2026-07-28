@php
    use Filament\Support\Enums\IconSize;
    use Illuminate\View\ComponentAttributeBag;

    use function Filament\Support\generate_icon_html;

    $label = mekaya()->documentationLabel();
    $url = mekaya()->documentationUrl();
    $opensInNewTab = mekaya()->documentationOpensInNewTab();
@endphp

<div class="mky-sidebar-group">
    <ul role="list" class="mky-sidebar-group-items">
        <li class="mky-sidebar-item">
            <a
                href="{{ $url }}"
                @if ($opensInNewTab)
                    target="_blank"
                    rel="noopener noreferrer"
                @endif
                class="mky-sidebar-item-link"
                x-tooltip="{
                    content: @js($label),
                    placement: document.dir === 'rtl' ? 'left' : 'right',
                    theme: $store.theme,
                    onShow: () => $store.sidebar.isCollapsed,
                }"
            >
                {{
                    generate_icon_html(
                        'heroicon-o-book-open',
                        attributes: (new ComponentAttributeBag)->class(['mky-sidebar-item-icon']),
                        size: IconSize::Large,
                    )
                }}

                <span
                    class="mky-sidebar-item-label"
                    x-cloak
                    x-show="! $store.sidebar.isCollapsed"
                    x-transition:enter="transition-opacity duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                >
                    {{ $label }}
                </span>
            </a>
        </li>
    </ul>
</div>
