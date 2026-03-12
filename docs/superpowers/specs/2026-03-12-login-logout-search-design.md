# Login, Logout & Search — Modern Override Design

**Date:** 2026-03-12
**Branch:** claude/friendly-payne
**Status:** Draft

## Overview

Three small changes to complete the user-facing Twenty26 experience:

1. **Login screen** — Modern `account/login.tpl.php` override with `idno-editor` card styling
2. **Logout link** — Add "Log out" nav item to the sidebar, below Settings
3. **Search modal** — Replace the dead search link with a modal overlay triggered from the sidebar

## What Changes

**New files:**
- `Themes/Twenty26/templates/modern/account/login.tpl.php`
- `Themes/Twenty26/templates/modern/shell/search.tpl.php`
- `Themes/Twenty26/templates/modern/forms/link.tpl.php` — vanilla JS override of the default jQuery-based link form

**Modified files:**
- `Themes/Twenty26/templates/modern/shell/nav.tpl.php` — add logout link and search modal trigger
- `Themes/Twenty26/templates/modern/shell.tpl.php` — add `shell/search` draw call
- `Themes/Twenty26/src/css/components/navigation.css` — add button reset for `.idno-nav-item` on `<button>` elements

**Styling** uses existing classes: `idno-editor`, `idno-form-field`, `idno-input`, `idno-btn`, `idno-btn-primary`, `idno-nav-item`, `idno-nav-icon`, `idno-modal-overlay`, `idno-modal`, `idno-modal-header`, `idno-modal-body`, `idno-modal-close`, `idno-modal-title`. One small CSS addition for button resets in the nav (see section 3).

**No JS module changes.** Alpine.js logic is inline in the templates.

---

## 1. Login Screen

### Layout

Centered card inside the existing site shell (rendered via `drawPage()` — the sidebar nav is still visible). The card is narrow and vertically centered in the content area.

```
+--sidebar--+--------content area---------+
|  Home     |                              |
|  Search   |   +---------------------+   |
|           |   | Welcome back!       |   |
|           |   |                     |   |
|           |   | [email/username___] |   |
|           |   | [password________] |   |
|           |   | [CAPTCHA if on]    |   |
|           |   | [    Sign in     ] |   |
|           |   |                     |   |
|           |   | Forgot? · Register  |   |
|           |   +---------------------+   |
|           |                              |
+-----------+------------------------------+
```

### Template: `account/login.tpl.php`

Uses `idno-editor` card with constrained width, centered:

```php
<div style="max-width:24rem;margin:2rem auto">
    <div class="idno-editor" style="text-align:center">
        <h4 class="idno-editor-heading">
            <?= \Idno\Core\Idno::site()->language()->_('Welcome back!') ?>
        </h4>
        <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>session/login" method="post">
            <!-- fields -->
        </form>
    </div>
</div>
```

**Edge case:** If a logged-in user visits `/session/login`, the `Login.php` handler already redirects them to the homepage (line 19). No template-level handling needed.

### Form fields

All fields use `idno-form-field` / `idno-input` / `idno-btn-primary` classes:

1. **Email/username** — `<input type="text" name="email" class="idno-input">` with placeholder
2. **Password** — `<input type="password" name="password" class="idno-input">` with placeholder
3. **CAPTCHA** — `<?php echo $this->__(['action' => '/session/login'])->draw('forms/input/captcha'); ?>` (same as default)
4. **Submit** — `<button type="submit" class="idno-btn idno-btn-primary" style="width:100%">Sign in</button>`
5. **Forward URL** — Hidden input `name="fwd"` with value from `$vars['fwd']`, `$_SERVER['HTTP_REFERER']`, or site URL (same logic as default)
6. **CSRF** — `<?php echo \Idno\Core\Idno::site()->actions()->signForm('/session/login'); ?>`

### Links below the form

- **Registration link** — shown only when `open_registration == true && canAddUsers()` (same conditional as default)
- **Forgot password** — always shown, links to `account/password`

Both styled as subtle text links, centered below the form.

### What we're NOT doing

- **No standalone shell** — uses the normal site shell. The centered card is visually sufficient.
- **No autofocus script** — the `autofocus` HTML attribute on the email input replaces the jQuery `$('#inputEmail').focus()` from the default template.

---

## 2. Logout Link

### Location

New nav item in `shell/nav.tpl.php`, placed after Settings and before the New Post button. Only visible when logged in.

### Nav order (logged in, admin)

```
Home
Profile
Search
Drafts
Settings
Log out        ← new
[+ New Post]
```

### Implementation

Uses `\Idno\Core\Idno::site()->actions()->createLink()` which generates a hidden form with CSRF token and a link that submits it on click.

**jQuery dependency:** The default `forms/link.tpl.php` template generates `onclick="$('#formId').submit()"` — jQuery syntax. Since Twenty26 does not load jQuery (it uses Alpine.js instead), we need a modern override at `Themes/Twenty26/templates/modern/forms/link.tpl.php` that replaces `$('#...')` with `document.getElementById('...').submit()`. This is a one-line change in the onclick handler — the rest of the template is identical.

```php
<?php if (!empty($user)) { ?>
<li>
    <?= \Idno\Core\Idno::site()->actions()->createLink(
        \Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/logout',
        '<svg class="idno-nav-icon" ...>...</svg><span class="idno-nav-label">'
            . \Idno\Core\Idno::site()->language()->_('Log out')
            . '</span>',
        [],
        ['class' => 'idno-nav-item']
    ) ?>
</li>
<?php } ?>
```

### Nav placement

The logout `<li>` is placed as the last item inside `<ul class="idno-nav-items">`, after Settings (admin-only) or after Drafts (non-admin). Since it's inside the `if (!empty($user))` block, it only appears when logged in. The New Post `<button>` remains outside the `<ul>` at the bottom of the nav.

**Icon:** Lucide "log-out" icon (inline SVG), matching the style of other nav icons.

---

## 3. Search Modal

### Behavior

1. Clicking "Search" in the sidebar dispatches an `open-search` Alpine.js event (same pattern as the compose modal's `open-compose`)
2. A modal overlay appears with a single search input, auto-focused
3. User types a query and presses Enter → navigates to `/?q=<query>`
4. Escape key or clicking outside closes the modal
5. No results preview — just a quick way to submit a search query

### Template: `shell/search.tpl.php`

Drawn by `shell.tpl.php` alongside the compose modal. Uses existing `idno-modal-*` CSS classes:

```php
<div x-data="{ open: false, query: '' }"
     x-show="open"
     x-cloak
     x-on:open-search.window="open = true; $nextTick(() => $refs.searchInput.focus())"
     x-on:keydown.escape.window="open = false"
     class="idno-modal-overlay">
    <div class="idno-modal" style="max-width:32rem" x-on:click.outside="open = false">
        <div class="idno-modal-header">
            <h3 class="idno-modal-title"><?= \Idno\Core\Idno::site()->language()->_('Search') ?></h3>
            <button type="button" class="idno-modal-close" x-on:click="open = false"><!-- X SVG --></button>
        </div>
        <div class="idno-modal-body">
            <form x-on:submit.prevent="if (query.trim()) window.location.href = '/?q=' + encodeURIComponent(query.trim())"
                  style="display:flex;gap:0.5rem">
                <input type="search" x-model="query" x-ref="searchInput"
                       class="idno-input" style="flex:1"
                       aria-label="<?= \Idno\Core\Idno::site()->language()->_('Search') ?>"
                       placeholder="<?= \Idno\Core\Idno::site()->language()->_('Search posts...') ?>">
                <button type="submit" class="idno-btn idno-btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Search') ?></button>
            </form>
        </div>
    </div>
</div>
```

Uses `x-on:` prefix (long form) to match the existing compose modal convention.

### Nav trigger change

The Search nav item in `nav.tpl.php` changes from a link to a button that dispatches the event:

```php
<li>
    <button type="button" class="idno-nav-item" x-on:click="$dispatch('open-search')">
        <svg class="idno-nav-icon" ...><!-- search icon --></svg>
        <span class="idno-nav-label"><?= \Idno\Core\Idno::site()->language()->_('Search') ?></span>
    </button>
</li>
```

### Button reset CSS

Add to `navigation.css` — a `button.idno-nav-item` reset so buttons render identically to links:

```css
button.idno-nav-item {
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font: inherit;
}
```

This is the only CSS change in this spec.

### Shell integration

Add `<?php echo $template->draw('shell/search'); ?>` to `shell.tpl.php`, next to the existing compose modal draw call.

### What we're NOT doing

- **No search results preview** — just a text input that navigates on submit
- **No keyboard shortcut** (e.g., Cmd+K) — can be added later
- **No search history** — YAGNI

---

## Data Flow

### Login
1. GET `/session/login` → `Login.php::getContent()` → draws `account/login` template → `drawPage()` renders through shell
2. POST `/session/login` → `Login.php::postContent()` → validates credentials → redirects (no changes to backend)

### Logout
1. Click "Log out" → `createLink()` submits hidden form to POST `/session/logout`
2. `Logout.php::postContent()` → logs user off → redirects to referer (no changes to backend)

### Search
1. Click Search → Alpine.js opens modal
2. Type query, press Enter → `window.location.href = '/?q=...'`
3. Homepage receives `q` parameter → existing search/filter logic handles it (no changes to backend)
