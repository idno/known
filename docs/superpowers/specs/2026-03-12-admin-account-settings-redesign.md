# Admin & Account Settings Shell Redesign

Phase 2 of the Twenty26 theme transition: fix and polish the admin panel and account settings pages.

## Problem

The admin panel and account settings pages have broken rendering:

1. **Double sidebar rendering**: `admin/shell.tpl.php` wraps admin page content with its own sidebar, while `settings-shell.tpl.php` also provides a sidebar — creating ghost duplicate menus in the content area.
2. **Plugin admin pages broken**: Plugin-contributed pages (e.g., ActivityPub) render with the wrong shell or show duplicated ghost menus.
3. **Account settings unstyled**: No Twenty26 override for `account/menu.tpl.php` — falls back to Bootstrap `<ul class="nav"><li>` markup, rendering as unstyled plain text links.
4. **Theme isolation**: The current `settings-shell.tpl.php` draws from `shell/*` subtemplates. When Twenty26 becomes the core default theme and other themes override `shell/*` for their own look, those changes bleed into admin/settings pages.

## Design Goals

- Fix all rendering bugs (ghost menus, broken plugin pages, unstyled account settings)
- Isolate admin and account shells from theme `shell/*` subtemplates so other themes can't accidentally break them
- Preserve plugin extensibility via existing `admin/menu/items` and `account/menu/items` extension points
- Polish forms, cards, and content to match the Twenty26 design language
- Design bar: Bluesky/Twitter settings

## Architecture: Self-Contained Shells

### Core Principle

Admin and account settings each get their own self-contained shell template that does NOT draw from `shell/*` subtemplates. Each shell is a complete HTML document — `<html>`, `<head>`, CSS, body, JS — with CSS/JS loaded via hardcoded `<link>` and `<script>` tags pointing to Twenty26's built assets.

This means a theme overriding `shell/nav`, `shell/css`, or `shell/footerjavascript` has zero effect on admin or account settings pages.

### What the new shells must replicate from `shell/*`

The current `settings-shell.tpl.php` draws ~15 `shell/*` subtemplates. The new self-contained shells need to replicate only the essential ones:

**Must include (hardcoded directly):**
- CSS loading — `<link>` to `Themes/Twenty26/dist/modern.min.css`
- JS loading — `<script>` to `Themes/Twenty26/dist/modern.min.js` (Alpine.js + modules)
- Messages — flash messages / alerts (draw `shell/messages` or inline equivalent)
- Form data — CSRF tokens and form helpers (draw `shell/form-data`)
- Basic metatags — charset, viewport

**Intentionally dropped (not needed for admin/settings):**
- `shell/nav` — no main site navigation in admin/settings
- `shell/opengraph`, `shell/structured-data`, `shell/dublincore` — admin pages aren't indexed
- `shell/syndication`, `shell/activitypub` — not relevant
- `shell/monetization`, `shell/amp` — not relevant
- `shell/bootstrap` — legacy CSS/JS not used by Twenty26

### Shell Override Registration

Change the `addUrlShellOverride` calls to use dedicated shells:

**Before:**
```php
// Idno/Core/Admin.php
addUrlShellOverride('admin', 'settings-shell');

// Idno/Core/Account.php
addUrlShellOverride('account', 'settings-shell');
```

**After:**
```php
// Idno/Core/Admin.php
addUrlShellOverride('admin', 'admin-shell');

// Idno/Core/Account.php
addUrlShellOverride('account', 'account-shell');
```

### Template File Changes

**Create:**
- `Themes/Twenty26/templates/modern/admin-shell.tpl.php` — self-contained admin page shell
- `Themes/Twenty26/templates/modern/account-shell.tpl.php` — self-contained account settings shell
- `Themes/Twenty26/templates/modern/account/menu.tpl.php` — modern account nav with icons

**Remove:**
- `Themes/Twenty26/templates/modern/admin/shell.tpl.php` — body wrapper that causes double-rendering
- `Themes/Twenty26/templates/modern/settings-shell.tpl.php` — replaced by the two dedicated shells

**Update:**
- All Twenty26 admin page templates (`admin/home.tpl.php`, `admin/plugins.tpl.php`, `admin/users.tpl.php`, `admin/themes.tpl.php`, `admin/email.tpl.php`, `admin/import.tpl.php`, `admin/export.tpl.php`, `admin/logs.tpl.php`, `admin/statistics.tpl.php`) — remove any `draw('admin/shell')` wrapper calls, since the new `admin-shell.tpl.php` page shell provides the sidebar directly. These templates should render only their content, not a layout wrapper.

## Admin Shell

### Layout

```
+--[dark sidebar 220px]--+--[content area]------------------+
| Idno (logo)             | Page Title                       |
|                         | Description text                 |
| Dashboard      (active) |                                  |
| Themes                  | +--[settings-card]-------------+ |
| Plugins                 | | Form fields                   | |
| Users                   | | ...                           | |
| Email                   | +-------------------------------+ |
| ActivityPub  (plugin)   |                                  |
| ─────────────           | [Save changes]                   |
| Statistics              |                                  |
| Import                  |                                  |
| Export                   |                                  |
|                         |                                  |
| ← Back to site          |                                  |
+-------------------------+----------------------------------+
```

### HTML Structure

```html
<body class="admin-template">
  <div class="idno-admin-shell">
    <aside class="idno-admin-sidebar">
      <a class="idno-admin-logo" href="...">Site Name</a>
      <nav class="idno-admin-nav">
        <!-- Native nav items as <a class="idno-admin-nav-item"> -->
        <!-- Plugin extension point: admin/menu/items (renders <li><a> markup) -->
        <div class="idno-admin-nav-divider"></div>
        <!-- More native nav items -->
      </nav>
      <div class="idno-admin-back">
        <a href="...">← Back to site</a>
      </div>
    </aside>
    <main class="idno-admin-main">
      <div class="idno-admin-content">
        <!-- messages -->
        <!-- page body -->
      </div>
    </main>
  </div>
</body>
```

### Sidebar Styling

- Dark background (`--color-slate-800` / `#1e293b`)
- Nav items: muted text, icon + label, rounded hover state with subtle white overlay
- Active state: slightly brighter background overlay, white text, full opacity icon
- "Back to site" link pinned to bottom with top border separator
- Site name/logo at top with bottom border separator

## Account Settings Shell

### Layout

```
+--[light sidebar 200px]-+--[content area]------------------+
| Account (header)        | Page Title                       |
|                         | Description text                 |
| Settings       (active) |                                  |
| Tools & Apps            | +--[settings-card]-------------+ |
| IndiePub     (plugin)   | | Form fields                   | |
| Export Data             | | ...                           | |
|                         | +-------------------------------+ |
|                         |                                  |
|                         | [Save updates]                   |
|                         |                                  |
| ← Back to site          |                                  |
+-------------------------+----------------------------------+
```

### HTML Structure

```html
<body class="account-template">
  <div class="idno-account-shell">
    <nav class="idno-account-sidebar">
      <div class="idno-account-header">Account</div>
      <div class="idno-account-nav">
        <!-- Native nav items as <a class="idno-account-nav-item"> -->
        <!-- Plugin extension point: account/menu/items (renders <li><a> markup) -->
      </div>
      <div class="idno-account-back">
        <a href="...">← Back to site</a>
      </div>
    </nav>
    <main class="idno-account-main">
      <div class="idno-account-content">
        <!-- messages -->
        <!-- page body -->
      </div>
    </main>
  </div>
</body>
```

### Sidebar Styling

- White background with right border separator
- "Account" header at top with bottom border
- Nav items: gray text, icon + label, rounded hover with light gray background
- Active state: light blue background, blue text, blue icon
- "Back to site" link pinned to bottom with top border separator

### Account Menu Template

New `Themes/Twenty26/templates/modern/account/menu.tpl.php`:

```html
<nav class="idno-account-nav">
  <a href=".../account/settings/" class="idno-account-nav-item [active]">
    <!-- settings icon --> Settings
  </a>
  <a href=".../account/settings/tools/" class="idno-account-nav-item [active]">
    <!-- wrench icon --> Tools & Apps
  </a>
  <!-- Extension point: account/menu/items -->
  <a href=".../account/export/" class="idno-account-nav-item [active]">
    <!-- upload icon --> Export Data
  </a>
</nav>
```

## Plugin Extensibility

### Extension Points (Unchanged)

- `admin/menu/items` — plugins add admin menu entries
- `account/menu/items` — plugins add account settings entries

### Markup Mismatch

Plugins contribute `<li><a href="...">Label</a></li>` (old Bootstrap pattern). The Twenty26 admin and account navs use `<a class="idno-*-nav-item">` directly.

### CSS Adaptation

Style `<li>` children within the nav containers to visually match native nav items. The CSS properties below should match whatever the `.idno-admin-nav-item` and `.idno-account-nav-item` classes use — the exact values will be determined during implementation from the existing `admin.css` design tokens:

```css
/* Admin nav — normalize plugin <li> items to match .idno-admin-nav-item */
.idno-admin-nav li {
  list-style: none;
}
.idno-admin-nav li a {
  /* Mirror .idno-admin-nav-item properties exactly */
}
.idno-admin-nav li a:hover {
  /* Mirror .idno-admin-nav-item:hover */
}
.idno-admin-nav li.active a {
  /* Mirror .idno-admin-nav-item.active */
}

/* Account nav — normalize plugin <li> items to match .idno-account-nav-item */
.idno-account-nav li {
  list-style: none;
}
.idno-account-nav li a {
  /* Mirror .idno-account-nav-item properties exactly */
}
.idno-account-nav li a:hover {
  /* Mirror .idno-account-nav-item:hover */
}
.idno-account-nav li.active a {
  /* Mirror .idno-account-nav-item.active */
}
```

### Plugin Admin Page Content

Plugin pages (e.g., ActivityPub admin) set `$t->body` and call `$t->drawPage()`. Since the URL matches `/admin/...`, the shell override renders inside `admin-shell.tpl.php`. The plugin's content appears in the content area with the admin sidebar provided by the shell. No plugin code changes needed.

## CSS Changes

### Existing Admin CSS (`admin.css`)

Already contains `.idno-admin-shell`, `.idno-admin-sidebar`, `.idno-admin-nav`, `.idno-admin-nav-item`, `.idno-admin-main`, `.idno-admin-content` styles. These need minor updates:

- Remove the `margin-left: -14rem` inline style hack from the old `admin/shell.tpl.php` (goes away when that template is deleted)
- Keep `.idno-admin-sidebar { width: 14rem }` and `.idno-admin-main { margin-left: 14rem }` — these correctly position the fixed sidebar and offset content
- The new `admin-shell.tpl.php` uses these classes directly as the page layout, so no negative margin compensation is needed

### New Account CSS

Add to existing `admin.css` or create `account.css`:

- `.idno-account-shell` — flex container, full height
- `.idno-account-sidebar` — white background, right border, 200px width
- `.idno-account-header` — "Account" label at top
- `.idno-account-nav` — flex column, gap-based spacing
- `.idno-account-nav-item` — nav link with icon, hover/active states
- `.idno-account-back` — "Back to site" link at bottom
- `.idno-account-main` — flex-grow content area with subtle background
- `.idno-account-content` — max-width constrained content

### Shared Form Components

Both admin and account pages use existing form classes from `forms.css`:
- `.idno-input`, `.idno-textarea`, `.idno-select` for form controls
- `.idno-label` for labels

Button styles in `buttons.css`: `.idno-btn-primary` and `.idno-btn-ghost` already exist. Additional button variants to add:
- `.idno-btn-enable` — green solid button (plugin enable)
- `.idno-btn-disable` — outline button with red hover state (plugin disable)

The settings-card component (`.idno-admin-card` or new `.idno-settings-card`) wraps form groups with a white background, border, and border-radius.

### Plugin List Styling

The plugins page needs updated styling for the plugin items:
- Plugin row: flex layout with icon, info (name/version/author/description), and action button
- Enable button: green solid style
- Disable button: outline style, red hover state
- Plugin icon: colored rounded square
- Version/author: muted secondary text

## Core PHP Changes

Two one-line changes:

1. **`Idno/Core/Admin.php`**: Change `addUrlShellOverride('admin', 'settings-shell')` to `addUrlShellOverride('admin', 'admin-shell')`
2. **`Idno/Core/Account.php`**: Change `addUrlShellOverride('account', 'settings-shell')` to `addUrlShellOverride('account', 'account-shell')`

## What Does NOT Change

- Plugin code — fully backwards compatible
- Admin page controllers — they already set `$t->body` and call `$t->drawPage()`
- The CSS build system — just adding to existing component files
- `admin/menu.tpl.php` — already works with the new structure
- Template extension points — `admin/menu/items` and `account/menu/items` unchanged

## Responsive Behavior

The admin sidebar already has a `@media (max-width: 768px)` rule in `admin.css` that converts it to a bottom bar on mobile. The account settings shell should follow the same pattern: on viewports narrower than 768px, the account sidebar collapses to a horizontal scrollable nav bar at the top of the content area, with the "Account" header hidden and nav items displayed in a row.

## Build System Note

The CSS build uses Tailwind v4 via PostCSS (`@tailwindcss/postcss` in `postcss.config.js`). There is no separate `tailwind.config.js` — Tailwind configuration is handled via CSS directives in `main.css`. The build tool is Vite (`vite.config.js`). New CSS component files need to be imported in `main.css`.

## Visual Reference

Interactive mockups are in `.superpowers/brainstorm/75779-1773347656/full-mockup-v2.html` showing:
1. Admin Dashboard with dark sidebar and site settings form
2. Admin Plugins page with enable/disable buttons
3. Admin Plugin Page (ActivityPub) — plugin-contributed page in admin shell
4. Account Settings with light sidebar and form
5. Account Tools page
