<div class="mky-sidebar">
    @foreach ($navigation as $group)
        @php
            $groupLabel = $group->getLabel();
            $groupItems = $group->getItems();
            $groupKey = filled($groupLabel) ? $groupLabel : 'default';
        @endphp

        <div
            class="mky-sidebar-group"
            x-data="{ label: @js($groupKey) }"
        >
            @if (filled($groupLabel))
                <button
                    type="button"
                    class="mky-sidebar-group-label w-full"
                    x-on:click="$store.sidebar.toggleGroup(label)"
                >
                    <span
                        x-show="! $store.sidebar.isCollapsed"
                        x-transition:enter="transition-opacity duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                    >{{ $groupLabel }}</span>

                    <span
                        class="mky-sidebar-group-toggle transition-transform"
                        x-show="! $store.sidebar.isCollapsed"
                        x-bind:class="{ 'rotate-180': $store.sidebar.isGroupCollapsed(label) }"
                    >
                        <x-filament::icon
                            :icon="\Filament\Support\Icons\Heroicon::ChevronUp"
                            class="size-4"
                        />
                    </span>
                </button>
            @endif

            <ul
                role="list"
                class="mky-sidebar-group-items"
                @if (filled($groupLabel))
                    x-show="! $store.sidebar.isGroupCollapsed(label)"
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
</div>
