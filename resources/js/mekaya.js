import sidebarStore from './sidebar'

document.addEventListener('alpine:init', () => {
  // Filament owns the theme store and `theme-changed` lifecycle. Mekaya only
  // replaces the sidebar store, avoiding duplicate theme listeners/effects.
  window.Alpine.store('sidebar', sidebarStore())
  window.Alpine.store('sidebar').init()
})
