const defaultBreakpoint = 1024
const filamentPersistKeys = {
  isOpen: 'isOpen',
  isOpenDesktop: 'isOpenDesktop',
  collapsedGroups: 'collapsedGroups',
}
const legacyCollapsedGroupsKey = 'sidebar-collapsed-groups'

const readStored = (key, fallback = null) => {
  try {
    const raw = localStorage.getItem(key)

    if (raw === null || raw === 'null') {
      return fallback
    }

    return JSON.parse(raw)
  } catch {
    return fallback
  }
}

const normalizeGroups = (groups, fallback = []) => (Array.isArray(groups) ? groups : fallback)

export default (config = {}) => {
  const persistKeys = {
    isOpen: config.persistKeys?.isOpen ?? 'sidebar-is-open',
    isCollapsed: config.persistKeys?.isCollapsed ?? 'sidebar-is-collapsed',
    collapsedGroups: config.persistKeys?.collapsedGroups ?? filamentPersistKeys.collapsedGroups,
  }

  const storedCollapsed = readStored(persistKeys.isCollapsed)
  const storedDesktopOpen = readStored(filamentPersistKeys.isOpenDesktop)
  const isCollapsed =
    typeof storedCollapsed === 'boolean'
      ? storedCollapsed
      : typeof storedDesktopOpen === 'boolean'
        ? !storedDesktopOpen
        : false
  const collapsedGroups = normalizeGroups(
    readStored(
      persistKeys.collapsedGroups,
      readStored(legacyCollapsedGroupsKey, config.defaultCollapsedGroups ?? []),
    ),
    config.defaultCollapsedGroups ?? [],
  )

  return {
    isOpen: readStored(persistKeys.isOpen, readStored(filamentPersistKeys.isOpen, false)) === true,
    isOpenDesktop: !isCollapsed,
    isCollapsed,
    collapsedGroups,
    collapsible: config.collapsible ?? false,
    currentPath: window.location.pathname,
    scrollTop: 0,
    initialized: false,
    resizeObserver: null,
    resizeListener: null,

    breakpoint: config.breakpoint ?? defaultBreakpoint,
    persistKeys,

    init() {
      // Alpine automatically calls a store's init() method. Mekaya also calls
      // it explicitly for backwards compatibility, so initialization must be
      // idempotent to avoid duplicate observers and navigation listeners.
      if (this.initialized) return

      this.initialized = true
      this.loadConfigFromDOM()

      if (!this.collapsible) {
        this.isOpenDesktop = true
      }

      this.applyViewportState()
      this.setUpResizeObserver()
      this.setupNavigationListener()
    },

    loadConfigFromDOM() {
      const body = document.body
      const breakpoint = body.dataset.sidebarBreakpoint
      const collapsible = body.dataset.sidebarCollapsible

      if (breakpoint) {
        const parsedBreakpoint = Number.parseInt(breakpoint, 10)

        if (Number.isFinite(parsedBreakpoint)) {
          this.breakpoint = parsedBreakpoint
        }
      }

      if (collapsible !== undefined) {
        this.collapsible = collapsible === 'true'
      } else if (
        body.classList.contains('fi-body-has-sidebar-collapsible-on-desktop') ||
        body.classList.contains('fi-body-has-sidebar-fully-collapsible-on-desktop')
      ) {
        this.collapsible = true
      }
    },

    isDesktop() {
      return window.innerWidth >= this.breakpoint
    },

    persistState(key, value) {
      try {
        localStorage.setItem(key, JSON.stringify(value))
      } catch {
        // Storage may be unavailable in hardened/private browsing contexts.
      }
    },

    persistOpenState() {
      this.persistState(this.persistKeys.isOpen, this.isOpen)

      if (this.persistKeys.isOpen !== filamentPersistKeys.isOpen) {
        this.persistState(filamentPersistKeys.isOpen, this.isOpen)
      }
    },

    persistDesktopState() {
      this.persistState(this.persistKeys.isCollapsed, !this.isOpenDesktop)
      this.persistState(filamentPersistKeys.isOpenDesktop, this.isOpenDesktop)
    },

    persistCollapsedGroups() {
      this.persistState(this.persistKeys.collapsedGroups, this.collapsedGroups)

      if (this.persistKeys.collapsedGroups !== filamentPersistKeys.collapsedGroups) {
        this.persistState(filamentPersistKeys.collapsedGroups, this.collapsedGroups)
      }

      if (this.persistKeys.collapsedGroups !== legacyCollapsedGroupsKey) {
        this.persistState(legacyCollapsedGroupsKey, this.collapsedGroups)
      }
    },

    syncCollapsedClass() {
      document.documentElement.classList.toggle(
        'sidebar-collapsed',
        this.isDesktop() && this.isCollapsed,
      )
    },

    applyViewportState() {
      if (this.isDesktop()) {
        this.isCollapsed = this.collapsible ? !this.isOpenDesktop : false
        this.isOpen = !this.isCollapsed
      } else {
        // A collapsed desktop preference must never make the mobile drawer
        // icon-only. Preserve isOpenDesktop, but render the mobile navigation
        // in its full form and start with the drawer closed.
        this.isCollapsed = false
        this.isOpen = false
      }

      this.syncCollapsedClass()
    },

    setDesktopOpen(isOpen, persist = true) {
      this.isOpenDesktop = this.collapsible ? isOpen : true
      this.isCollapsed = !this.isOpenDesktop

      if (this.isDesktop()) {
        this.isOpen = this.isOpenDesktop
      }

      this.syncCollapsedClass()

      if (persist) {
        this.persistDesktopState()
        this.persistOpenState()
      }
    },

    // Sidebar visibility. These mirror Filament's store contract: on mobile
    // they open/close the drawer; on desktop they expand/collapse the sidebar.
    open() {
      if (this.isDesktop()) {
        this.expand()

        return
      }

      this.isOpen = true
      this.persistOpenState()
    },

    close() {
      if (this.isDesktop()) {
        this.collapse()

        return
      }

      this.isOpen = false
      this.persistOpenState()
    },

    toggle() {
      if (this.isDesktop()) {
        this.toggleCollapse()

        return
      }

      this.isOpen ? this.close() : this.open()
    },

    // Sidebar collapse (desktop)
    collapse() {
      if (!this.collapsible) return

      this.setDesktopOpen(false)
    },

    expand() {
      this.setDesktopOpen(true)
    },

    toggleCollapse() {
      if (!this.collapsible) return

      this.setDesktopOpen(this.isCollapsed)
    },

    // Group management. Filament-compatible aliases are kept alongside the
    // original Mekaya names so render hooks and third-party panel components
    // can safely use either contract.
    isGroupCollapsed(label) {
      return this.collapsedGroups.includes(label)
    },

    groupIsCollapsed(label) {
      return this.isGroupCollapsed(label)
    },

    collapseGroup(label) {
      if (this.isGroupCollapsed(label)) return

      this.collapsedGroups = this.collapsedGroups.concat(label)
      this.persistCollapsedGroups()
    },

    expandGroup(label) {
      if (!this.isGroupCollapsed(label)) return

      this.collapsedGroups = this.collapsedGroups.filter(
        (collapsedGroup) => collapsedGroup !== label,
      )
      this.persistCollapsedGroups()
    },

    toggleGroup(label) {
      this.isGroupCollapsed(label) ? this.expandGroup(label) : this.collapseGroup(label)
    },

    toggleCollapsedGroup(label) {
      this.toggleGroup(label)
    },

    // Responsive handling
    setUpResizeObserver() {
      this.resizeObserver?.disconnect()

      if (this.resizeListener) {
        window.removeEventListener('resize', this.resizeListener)
        this.resizeListener = null
      }

      let previousWidth = window.innerWidth

      const handleResize = () => {
        const currentWidth = window.innerWidth
        const wasDesktop = previousWidth >= this.breakpoint
        const isDesktop = currentWidth >= this.breakpoint

        if (wasDesktop && !isDesktop) {
          this.isOpenDesktop = !this.isCollapsed
          this.isOpen = false
          this.isCollapsed = false
          this.persistOpenState()
        } else if (!wasDesktop && isDesktop) {
          this.isOpen = this.isOpenDesktop
          this.isCollapsed = !this.isOpenDesktop
        }

        previousWidth = currentWidth
        this.syncCollapsedClass()
      }

      if (typeof ResizeObserver === 'function') {
        this.resizeObserver = new ResizeObserver(handleResize)
        this.resizeObserver.observe(document.body)
      } else {
        this.resizeListener = handleResize
        window.addEventListener('resize', this.resizeListener, { passive: true })
      }
    },

    // Alias retained for existing Mekaya integrations.
    setupResizeObserver() {
      this.setUpResizeObserver()
    },

    // Livewire integration
    refresh() {
      if (window.Livewire) {
        window.Livewire.dispatch('sidebar:refresh')
      }
    },

    // Navigation tracking
    setupNavigationListener() {
      document.addEventListener('livewire:navigate', () => {
        const navigation = document.querySelector(
          '#mekaya-desktop-sidebar .mky-si-scroll',
        )

        if (navigation) {
          this.scrollTop = navigation.scrollTop
        }
      })

      document.addEventListener('livewire:navigated', () => {
        this.currentPath = window.location.pathname
        this.setUpResizeObserver()

        window.requestAnimationFrame(() => {
          const navigation = document.querySelector(
            '#mekaya-desktop-sidebar .mky-si-scroll',
          )

          if (navigation && this.scrollTop) {
            navigation.scrollTop = this.scrollTop
          }
        })
      })
    },

    isActive(url) {
      if (!url) return false

      try {
        const link = new URL(url, window.location.origin)

        if (link.origin !== window.location.origin) return false

        const normalizePath = (path) => path.replace(/\/+$/, '') || '/'
        const currentPath = normalizePath(this.currentPath)
        const linkPath = normalizePath(link.pathname)

        if (currentPath === linkPath) return true

        return linkPath !== '/' && currentPath.startsWith(`${linkPath}/`)
      } catch {
        return false
      }
    },
  }
}
