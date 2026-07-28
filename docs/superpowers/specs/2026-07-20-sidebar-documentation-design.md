# Sidebar Documentation Link

## Goal
Add a Shopper-style Documentation item at the bottom of the Mekaya sidebar footer. Configurable on/off + custom URL for a more mature appshell.

## Decisions
- Placement: sticky sidebar footer (above notifications/user menu), desktop + mobile
- Config + `.env` defaults; `MekayaPlugin` fluent API overrides
- Default: disabled until explicitly enabled with a non-empty URL
- Opens in new tab by default
- Label defaults to translated "Documentation"; overridable via config/plugin

## Visibility rule
Show only when `enabled === true` AND URL is filled (plugin override wins over config).

## API
```php
MekayaPlugin::make()
    ->documentation('https://docs.example.com')
    ->documentation(false)
    ->documentation('https://docs.example.com', label: 'Docs', newTab: true);
```

## Config
`mekaya.documentation.{enabled,url,label,open_in_new_tab}` via `MEKAYA_DOCS_*` env keys.
