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

Style `<li>` children within the nav containers to visually match native nav items:

```css
/* Admin nav — normalize plugin <li> items */
.idno-admin-nav li {
  list-style: none;
}
.idno-admin-nav li a {
  /* Same visual properties as .idno-admin-nav-item */
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  color: var(--color-slate-400);
  font-size: var(--text-sm);
  border-radius: var(--radius-md);
  text-decoration: none;
}
.idno-admin-nav li a:hover {
  background: rgba(255, 255, 255, 0.08);
  color: var(--color-slate-200);
}
.idno-admin-nav li.active a {
  background: rgba(255, 255, 255, 0.12);
  color: var(--color-slate-100);
  font-weight: 500;
}

/* Account nav — same pattern, light theme */
.idno-account-nav li { list-style: none; }
.idno-account-nav li a { /* match .idno-account-nav-item */ }
.idno-account-nav li a:hover { /* light hover */ }
.idno-account-nav li.active a { /* blue active state */ }
```

### Plugin Admin Page Content

Plugin pages (e.g., ActivityPub admin) set `$t->body` and call `$t->drawPage()`. Since the URL matches `/admin/...`, the shell override renders inside `admin-shell.tpl.php`. The plugin's content appears in the content area with the admin sidebar provided by the shell. No plugin code changes needed.

## CSS Changes

### Existing Admin CSS (`admin.css`)

Already contains `.idno-admin-shell`, `.idno-admin-sidebar`, `.idno-admin-nav`, `.idno-admin-nav-item`, `.idno-admin-main`, `.idno-admin-content` styles. These need minor updates to work as the page shell rather than a body wrapper (remove the negative margin hack).

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
- `.idno-button` for buttons

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

## Visual Reference

Interactive mockups are in `.superpowers/brainstorm/75779-1773347656/full-mockup-v2.html` showing:
1. Admin Dashboard with dark sidebar and site settings form
2. Admin Plugins page with enable/disable buttons
3. Admin Plugin Page (ActivityPub) — plugin-contributed page in admin shell
4. Account Settings with light sidebar and form
5. Account Tools page
