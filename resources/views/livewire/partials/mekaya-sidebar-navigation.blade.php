<div class="mky-sidebar">
    @php
        $navigationIdPrefix ??= 'sidebar';
    @endphp

    @foreach ($navigation as $group)
        @php
            $groupLabel = $group->getLabel();
            $groupItems = $group->getItems();
            $groupKey = filled($groupLabel) ? $groupLabel : 'default';
            $groupId = 'mekaya-' . $navigationIdPrefix . '-sidebar-group-' . $loop->index . '-' . md5($groupKey);
            $isGroupCollapsible = $group->isCollapsible();
            // Like Filament's own sidebar, never hide the page the user is on:
            // a group holding the active item opens even if it was stored or
            // configured (->collapsed()) as collapsed.
            $isGroupActive = filled($groupLabel) && $isGroupCollapsible && $group->isActive();
        @endphp

        <div
            class="mky-sidebar-group"
            x-data="{ label: @js($groupKey) }"
            @if ($isGroupActive)
                x-init="$store.sidebar.expandGroup(label)"
            @endif
            data-group-label="{{ $groupKey }}"
        >
            @if (filled($groupLabel))
                @if ($isGroupCollapsible)
                    <button
                        type="button"
                        class="mky-sidebar-group-label w-full"
                        x-cloak
                        x-show="! $store.sidebar.isCollapsed"
                        x-on:click="$store.sidebar.toggleCollapsedGroup(label)"
                        x-bind:aria-expanded="! $store.sidebar.groupIsCollapsed(label)"
                        aria-controls="{{ $groupId }}"
                    >
                        <span>{{ $groupLabel }}</span>

                        <span
                            class="mky-sidebar-group-toggle transition-transform"
                            x-bind:class="{ 'rotate-180': $store.sidebar.groupIsCollapsed(label) }"
                            aria-hidden="true"
                        >
                            <x-filament::icon
                                :icon="\Filament\Support\Icons\Heroicon::ChevronUp"
                                class="size-4"
                            />
                        </span>
                    </button>
                @else
                    <div
                        class="mky-sidebar-group-label w-full"
                        x-cloak
                        x-show="! $store.sidebar.isCollapsed"
                    >
                        <span>{{ $groupLabel }}</span>
                    </div>
                @endif
            @endif

            <ul
                id="{{ $groupId }}"
                role="list"
                class="mky-sidebar-group-items"
                @if (filled($groupLabel) && $isGroupCollapsible)
                    x-cloak
                    {{-- In icon-only mode the group header is hidden, so a collapsed
                         group could never be reopened: always show its icons. --}}
                    x-show="$store.sidebar.isCollapsed || ! $store.sidebar.groupIsCollapsed(label)"
                    x-collapse.duration.200ms
                @endif
            >
                @foreach ($groupItems as $item)
                    @include('mekaya::livewire.partials.mekaya-sidebar-item', [
                        'item' => $item,
                    ])
                @endforeach
            </ul>
        </div>
    @endforeach

    {{-- Plugin hooks (e.g. AI chat history) must live inside .mky-sidebar so
         nested Mekaya sidebar styles apply. --}}
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_NAV_END) }}
</div>
