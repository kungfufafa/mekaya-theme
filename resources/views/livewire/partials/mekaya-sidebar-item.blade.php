@php
    use Filament\Support\Enums\IconSize;
    use Illuminate\View\ComponentAttributeBag;

    use function Filament\Support\generate_href_html;
    use function Filament\Support\generate_icon_html;

    $isActive = $item->isActive();
    $hasActiveChildren = $item->isChildItemsActive();
    $childItems = $item->getChildItems();
    $hasChildItems = filled($childItems);
    $activeIcon = $item->getActiveIcon();
    $icon = ($isActive && $activeIcon) ? $activeIcon : $item->getIcon();
    $badge = $item->getBadge();
    $badgeColor = $item->getBadgeColor($badge);
    $badgeColor = is_string($badgeColor) ? $badgeColor : 'gray';
    $url = $item->getUrl();
@endphp

<li
    @class([
        'mky-sidebar-item',
        'mky-sidebar-item-active' => $isActive || $hasActiveChildren,
        'mky-items-has-child' => $hasChildItems,
    ])
>
    <a
        {{ generate_href_html($url, $item->shouldOpenUrlInNewTab()) }}
        x-on:click="window.innerWidth < $store.sidebar.breakpoint && $store.sidebar.close()"
        x-tooltip="{
            content: @js($item->getLabel()),
            placement: document.dir === 'rtl' ? 'left' : 'right',
            theme: $store.theme,
            onShow: () => $store.sidebar.isCollapsed,
        }"
        class="mky-sidebar-item-link {{ ($isActive || $hasActiveChildren) ? 'mky-active' : '' }}"
        @if ($isActive)
            aria-current="page"
        @endif
    >
        @if (filled($icon))
            {{
                generate_icon_html(
                    $icon,
                    attributes: (new ComponentAttributeBag)->class(['mky-sidebar-item-icon']),
                    size: IconSize::Large,
                )
            }}
        @else
            <span class="mky-sidebar-item-dot"></span>
        @endif

        <span
            class="mky-sidebar-item-label"
            x-cloak
            x-show="! $store.sidebar.isCollapsed"
            x-transition:enter="transition-opacity duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            {{ $item->getLabel() }}
        </span>

        @if (filled($badge) || $hasChildItems)
            <span
                class="mky-sidebar-item-nav"
                x-cloak
                x-show="! $store.sidebar.isCollapsed"
            >
                @if (filled($badge))
                    <span class="mky-sidebar-item-badge mky-sidebar-item-badge-{{ $badgeColor }}">
                        {{ $badge }}
                    </span>
                @endif

                @if ($hasChildItems)
                    <x-filament::icon
                        :icon="\Filament\Support\Icons\Heroicon::ChevronDown"
                        class="mky-sidebar-item-toggle size-4"
                    />
                @endif
            </span>
        @endif
    </a>

    @if ($hasChildItems)
        <ul class="mky-submenu {{ $isActive || $hasActiveChildren ? 'block' : '' }}">
            @foreach ($childItems as $childItem)
                @include('mekaya::livewire.partials.mekaya-sidebar-item', [
                    'item' => $childItem,
                ])
            @endforeach
        </ul>
    @endif
</li>
