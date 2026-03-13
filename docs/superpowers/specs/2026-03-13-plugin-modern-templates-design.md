# Plugin Modern Template Overrides

**Date:** 2026-03-13
**Status:** Draft

## Goal

Every stock Idno plugin that has admin or account settings pages must provide a `templates/modern/` equivalent so the Twenty26 theme renders them with the modern card-based UI, dark sidebar navigation with Lucide icons, and no jQuery.

## Scope

8 pages across 7 plugins:

| Plugin | Page Type | Template Path (default) | JS Complexity |
|--------|-----------|------------------------|---------------|
| ActivityPub | Admin | `activitypub/admin.tpl.php` | None |
| StaticPages | Admin | `staticpages/admin.tpl.php` | Heavy (sortable, AJAX) |
| Styles | Admin | `styles/admin.tpl.php` | Minimal (file input label) |
| StandardSiteSync | Admin | `standardsitesync/admin/settings.tpl.php` | None |
| StandardSiteSync | Account | `standardsitesync/account/settings.tpl.php` | Minimal (toggle details) |
| Webhooks | Admin | `webhooks/admin/home.tpl.php` | Moderate (dynamic form rows) |
| Bridgy | Account | `bridgy/account.tpl.php` | Significant (AJAX polling) |
| IndiePub | Account | `account/indiepub.tpl.php` | Minimal (show/hide) |

Plus 8 navigation menu item templates (StandardSiteSync needs separate files for admin and account despite sharing an icon).

## Architecture

### Template Location — Decentralized in Plugins

Modern templates live inside each plugin's own `templates/modern/` directory, mirroring the path structure under `templates/default/`. The Bonita template engine resolves templates by type, so when Twenty26 sets `templateType('modern')`, the engine automatically picks up `templates/modern/` variants.

Example structure for StaticPages:
```
IdnoPlugins/StaticPages/
├── templates/
│   ├── default/
│   │   └── staticpages/
│   │       ├── admin.tpl.php          (existing)
│   │       └── admin/
│   │           └── menu.tpl.php       (existing)
│   └── modern/
│       └── staticpages/
│           ├── admin.tpl.php          (new - modern page)
│           └── admin/
│               └── menu.tpl.php       (new - nav with icon)
```

This keeps plugins self-contained. Third-party plugins can ship their own modern templates without modifying the theme.

### No jQuery

All interactive behavior uses Alpine.js (preferred) or vanilla JavaScript. The modern shell does not load jQuery. Existing default templates remain unchanged.

### CSS

Modern templates use CSS classes already defined in `Themes/Twenty26/dist/modern.min.css`:
- `.admin-card` — white card with border and border-radius
- `.admin-card-header` — card title area
- `.admin-table` — clean borderless table
- Standard form classes from the theme

No plugin-specific CSS files needed — the existing theme classes cover all layouts.

## Navigation Menu Items

Each plugin provides a modern menu template that renders as a sidebar nav link with a Lucide icon using the theme's `shell/icon` helper.

### Icon Assignments

| Plugin | Nav Label | Lucide Icon | Path Match |
|--------|-----------|-------------|------------|
| ActivityPub | ActivityPub | `globe` | `/admin/activitypub/` |
| StaticPages | Pages | `file-text` | `/admin/staticpages/` |
| Styles | Custom CSS | `paintbrush` | `/admin/styles/` |
| StandardSiteSync (admin) | Standard.site Sync | `refresh-cw` | `/admin/standardsitesync/` |
| StandardSiteSync (account) | Standard.site Sync | `refresh-cw` | `/account/standardsitesync/` |
| Webhooks | Webhooks | `webhook` | `/admin/webhooks/` |
| Bridgy | Interactions | `share-2` | `/account/bridgy/` |
| IndiePub | IndiePub | `send` | `/account/indiepub/` |

### Menu Template Pattern

All modern menu templates follow this pattern (admin example):

```php
<?php
    $page = \Idno\Core\Idno::site()->currentPage();
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
?>
<a href="<?= $baseUrl ?>admin/activitypub/"
   class="idno-admin-nav-item<?php if ($page->doesPathMatch('/admin/activitypub/')) echo ' active'; ?>">
    <?= $this->__(['icon' => 'globe'])->draw('shell/icon') ?>
    <span><?= \Idno\Core\Idno::site()->language()->_('ActivityPub') ?></span>
</a>
```

Account menu items use `idno-account-nav-item` class instead.

### Icon Map Addition

The `paintbrush` icon needs to be added to `Themes/Twenty26/templates/modern/shell/icon.tpl.php`. All other icons (`globe`, `file-text`, `refresh-cw`, `webhook`, `share-2`, `send`) already exist.

## Page Designs

### 1. ActivityPub Admin (No JS)

Read-only dashboard showing federation status.

**Sections:**
- Warning banner if async queue is not enabled (yellow card)
- Stats row: Total Users count, Total AP Followers count
- Users table: username, AP handle, follower count, key status (✓ Generated / ✗ Missing)
- Configuration card: WebFinger, Shared Inbox, NodeInfo endpoint URLs

**Data source:** Template receives `$vars` with user list from existing `Pages/Admin/ActivityPub.php`.

### 2. StaticPages Admin (Alpine.js)

CRUD table for managing static pages and categories with drag-to-reorder.

**Sections:**
- Header with "Add new page" button (links to existing edit page)
- Pages table: drag handle, title, category, homepage icon, edit link, delete action
- Categories section: tag pills with add/remove, inline edit

**Interactive behavior (Alpine.js):**
- **Drag-to-reorder:** HTML5 Drag and Drop API via an Alpine component. Uses `draggable="true"`, `@dragstart`, `@dragover.prevent`, `@drop` events. On drop, sends a `fetch()` POST to the existing reorder endpoints (`/admin/staticpages/reorder/` and `/admin/staticpages/reorder/page`).
- **Add category:** Alpine `x-show` toggle to reveal the add form inline.
- **Edit category inline:** Alpine `x-show` toggle per row to swap display text for edit form.
- No external sortable library needed — HTML5 DnD is sufficient for simple list reordering.

**Reorder POST formats** (match existing server endpoints):
```js
// Category reorder: POST /admin/staticpages/reorder/
fetch(reorderUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ category: categoryName, position: newIndex })
})

// Page reorder: POST /admin/staticpages/reorder/page
fetch(pageReorderUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ page: pageId, position: newIndex })
})
```

**Delete actions** use `\Idno\Core\Idno::site()->actions()->createLink()` which generates a form with embedded CSRF token — same as the default template. This helper is template-engine-level and works without jQuery.

**CSRF tokens:** The reorder `fetch()` POST calls do not require CSRF tokens — the existing default template's jQuery `$.post()` calls also omit them, and the server handlers (`adminGatekeeper()`) do not validate tokens on these endpoints.

**Drag constraints:** Pages can only be reordered within their own category `<tbody>` — not dragged between categories (matching existing behavior). Visual feedback during drag uses a CSS class (e.g., `opacity: 0.5` on the dragged row, border highlight on the drop target).

**Touch support:** HTML5 DnD does not support touch devices natively. This is an acceptable limitation for an admin interface primarily used on desktop. If touch reordering is needed later, a lightweight polyfill can be added.

### 3. Custom CSS / Styles Admin (Vanilla JS)

CSS editor with file upload.

**Sections:**
- Textarea with monospace font for editing CSS
- File upload input with styled label (vanilla JS `change` event to show filename)
- Download current stylesheet link
- Save button (standard form POST)

**Interactive behavior (Vanilla JS):**
- File input `change` listener updates a filename display label — same pattern already used in the theme's file input styling.

### 4. StandardSiteSync Admin (No JS)

Simple toggle form.

**Sections:**
- Single checkbox: "Enable Standard.site sync site-wide"
- Explanatory text
- Save button (standard form POST)

### 5. Webhooks Admin (Alpine.js)

Dynamic form for managing webhook URLs.

**Sections:**
- List of webhook entries, each with name + URL text inputs and a remove button
- "Add webhook" link at bottom
- Save button

**Interactive behavior (Alpine.js):**
- `x-data` component with a `webhooks` array
- `addWebhook()` method pushes a new empty `{name: '', url: ''}` entry
- `removeWebhook(index)` method splices from the array
- `x-for` loop renders the dynamic rows
- Form inputs use `x-model` bound to array entries
- Form inputs use `name="titles[]"` and `name="webhooks[]"` to match the server-side handler (`$this->getInput('webhooks')` and `$this->getInput('titles')`)

### 6. Bridgy Account (Vanilla JS)

Service connection interface for Twitter/Bridgy.

**Sections:**
- Explanation of what Bridgy does
- Connection status card: connected (with disconnect form) or disconnected (with connect form)
- Forms POST directly to `brid.gy` external URLs (existing behavior preserved)

**Interactive behavior (Vanilla JS):**
- On page load, a `fetch()` GET request to `/account/bridgy/check/` with `X-Requested-With: XMLHttpRequest` header (GET, no CSRF token needed)
- If `data.changed` is true, reload the page to get updated connection status
- This replaces the jQuery approach of swapping innerHTML — a full page reload is simpler and avoids the complexity of partial DOM replacement

**Note:** The existing default template only shows Twitter/Bridgy. A separate `bridgy/facebook.tpl.php` template exists but is not drawn by `bridgy/account.tpl.php`. The modern template mirrors this — Twitter only. If Facebook support is re-enabled later, a separate modern template can be added.

### 7. IndiePub Account (Alpine.js)

Micropub client token management.

**Sections:**
- Table of authorized clients: client name/URL, authorized date, scopes, revoke button
- "Add Micropub Account" form (toggleable)

**Interactive behavior (Alpine.js):**
- `x-data="{ showAddForm: false }"` for toggling the add form visibility
- `x-show="showAddForm"` on the form container
- Revoke buttons use standard form POST with `confirm()` dialog

### 8. StandardSiteSync Account (Vanilla JS)

User connection flow for AT Protocol sync.

**Sections:**
- Connection status display (not connected / connected with handle)
- Connect form: handle input, connect button
- When connected: sync all posts button, disconnect button
- Collapsible technical details (DID, PDS info)

**Interactive behavior (Vanilla JS):**
- `<details>/<summary>` for technical info disclosure (native HTML, no JS needed)
- `confirm()` dialogs on disconnect and sync-all actions

## File Inventory

### New Files (16 total)

**Navigation menu templates (8):**
1. `IdnoPlugins/ActivityPub/templates/modern/activitypub/admin/menu.tpl.php`
2. `IdnoPlugins/StaticPages/templates/modern/staticpages/admin/menu.tpl.php`
3. `IdnoPlugins/Styles/templates/modern/styles/admin/menu.tpl.php`
4. `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/admin/menu.tpl.php`
5. `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/account/menu.tpl.php`
6. `IdnoPlugins/Webhooks/templates/modern/webhooks/admin/menu.tpl.php`
7. `IdnoPlugins/Bridgy/templates/modern/bridgy/menu.tpl.php`
8. `IdnoPlugins/IndiePub/templates/modern/account/menu/items/indiepub.tpl.php`

**Page content templates (8):**
9. `IdnoPlugins/ActivityPub/templates/modern/activitypub/admin.tpl.php`
10. `IdnoPlugins/StaticPages/templates/modern/staticpages/admin.tpl.php`
11. `IdnoPlugins/Styles/templates/modern/styles/admin.tpl.php`
12. `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/admin/settings.tpl.php`
13. `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/account/settings.tpl.php`
14. `IdnoPlugins/Webhooks/templates/modern/webhooks/admin/home.tpl.php`
15. `IdnoPlugins/Bridgy/templates/modern/bridgy/account.tpl.php`
16. `IdnoPlugins/IndiePub/templates/modern/account/indiepub.tpl.php`

### Modified Files (1)
17. `Themes/Twenty26/templates/modern/shell/icon.tpl.php` — add `paintbrush` icon SVG path

## Constraints

- **No jQuery** — Alpine.js first, vanilla JS fallback
- **No external JS libraries** — HTML5 DnD replaces html5sortable for StaticPages
- **No plugin-specific CSS** — use existing theme classes
- **Preserve all server-side behavior** — same form actions, same POST endpoints, same CSRF tokens via `signForm()`
- **Default templates untouched** — modern templates are additive only
