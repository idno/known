# Profile Edit Template — Modern Override Design

**Date:** 2026-03-12
**Branch:** claude/friendly-payne
**Status:** Draft

## Overview

Create a modern template override for the profile edit page (`entity/User/edit.tpl.php`) that replaces the Bootstrap 3 two-column grid layout with a single-column `idno-editor` card using existing Twenty26 CSS classes and Alpine.js for interactivity.

## What Changes

**New file:**
- `Themes/Twenty26/templates/modern/entity/User/edit.tpl.php`

**No CSS changes.** All styling uses existing classes: `idno-editor`, `idno-editor-heading`, `idno-form-field`, `idno-input`, `idno-textarea`, `idno-label`, `idno-btn`, `idno-btn-primary`, `idno-btn-ghost`, `idno-form-help`.

**No JS module changes.** Alpine.js logic is inline in the template (same pattern as access.tpl.php).

## Layout

Single-column card, consistent with post editor templates (Entry, Photo, Status):

```
+--------------------------------------+
| Edit your profile                    |
|                                      |
| [Avatar 80px]  Change photo          |
|                JPG, PNG or GIF       |
|                                      |
| Your name                            |
| [___________________________]        |
|                                      |
| About you                            |
| [___________________________]        |
| [___________________________]        |
|                                      |
| Your websites                        |
| Other places on the web...           |
| [https://werd.io_________] [x]      |
| [https://_________________] [x]      |
| + Add another website                |
|                                      |
| [Save Changes] [Cancel]             |
+--------------------------------------+
```

## Components

### 1. Card wrapper

Uses `idno-editor` card — same as post editors. Contains heading, form fields, and action buttons.

```php
<div class="idno-editor">
    <h4 class="idno-editor-heading"><?= \Idno\Core\Idno::site()->language()->_('Edit your profile') ?></h4>
    <!-- fields -->
</div>
```

Uses `<h4>` for the heading (not `<h1>` as in the default template) — this matches the `idno-editor-heading` pattern used in post editors (Entry, Photo) where the page shell already provides the top-level heading structure.

No description paragraph — the heading is self-explanatory.

**Note:** This template does NOT include `entity/edit/header` or `entity/edit/footer` — those are for content post editors (Entry, Photo, Status) and handle sharing tabs and post submission JS. The default User edit template doesn't include them either.

**Note:** This template does NOT include `content/extra` or `content/access` — those are for content posts. Profile editing doesn't have access controls or syndication.

### 2. Avatar section (Alpine.js)

Horizontal layout: circular avatar preview on the left, "Change photo" label-button on the right. Add `x-cloak` to this section to prevent flash before Alpine initializes the preview.

**Alpine component:** Stores the icon URL in the data object to avoid double-context escaping (JS string inside HTML attribute):
```php
<?php $icon_url = htmlspecialchars(json_encode($vars['user']->getIcon()), ENT_QUOTES, 'UTF-8'); ?>
<div class="idno-form-field" x-data="{ preview: null, icon: <?= $icon_url ?> }" x-cloak>
```
- `preview` — data URL from FileReader, or `null` to show current avatar
- `icon` — the current avatar URL from the server
- On file input `change`: read the selected file with `FileReader.readAsDataURL()`, set `preview` to the result
- Avatar `<img>` uses `:src="preview || icon"`

**Deliberate improvement:** Uses `$vars['user']->getIcon()` instead of the default template's `currentUser()->getIcon()`. The `$vars['user']` is the user being edited (set by the Edit page controller), which is correct when an admin edits another user's profile.

**File input:** `<input type="file" name="avatar" id="avatar" accept="image/*">` hidden with `display:none`. The `id="avatar"` is required for the `<label for="avatar">` association to work. Uses `<label for="avatar">` styled as `idno-btn idno-btn-ghost` with an inline camera SVG icon.

**Help text:** `<p class="idno-form-help">` below the button.

### 3. Name field

Standard form field, with i18n wrapping on the label:
```php
<div class="idno-form-field">
    <label class="idno-label" for="name"><?= \Idno\Core\Idno::site()->language()->_('Your name') ?></label>
    <input class="idno-input" type="text" id="name" name="name"
           value="<?= htmlspecialchars($vars['user']->getTitle()) ?>">
</div>
```

### 4. Bio / About you field

Plain textarea (not richtext — the default template uses a plain textarea, and profile descriptions are typically short):

```php
<div class="idno-form-field">
    <label class="idno-label" for="body"><?= \Idno\Core\Idno::site()->language()->_('About you') ?></label>
    <textarea class="idno-textarea" name="profile[description]" id="body"
              placeholder="<?= \Idno\Core\Idno::site()->language()->_('Tell people about yourself...') ?>"
    ><?= htmlspecialchars($vars['user']->getDescription()) ?></textarea>
</div>
```

### 5. Websites section (Alpine.js)

Dynamic URL list managed by Alpine.js.

**PHP data preparation:** Build the `$urls` array from user profile data, matching the default template's normalization logic:

```php
<?php
if (!empty($vars['user']->profile['url'])) {
    $urls = is_array($vars['user']->profile['url'])
        ? $vars['user']->profile['url']
        : [$vars['user']->profile['url']];
    $urls = array_values(array_filter(array_map(function($u) {
        return !empty($u) ? $this->fixURL($u) : '';
    }, $urls)));
}
if (empty($urls)) {
    $urls = [''];
}
?>
```

**Alpine component:** Wraps the websites section with `x-cloak` to prevent flash.
```php
<div class="idno-form-field" x-data="{ urls: <?= htmlspecialchars(json_encode($urls), ENT_QUOTES, 'UTF-8') ?> }" x-cloak>
```

**Each URL row:** Rendered with Alpine.js `x-for` inside a `<template>` wrapper (required by Alpine.js v3). Uses only `x-model` (not `:value`, which would conflict):
```html
<template x-for="(url, index) in urls" :key="index">
    <div style="display:flex;gap:0.5rem;align-items:center;margin-bottom:0.5rem">
        <input type="url" name="profile[url][]" class="idno-input"
               x-model="urls[index]" placeholder="https://">
        <button type="button" @click="urls.splice(index, 1)"
                x-show="urls.length > 1"><!-- X SVG --></button>
    </div>
</template>
```

- Remove button: only shown when `urls.length > 1` (always keep at least one row), with inline X SVG icon

**Add button:** `<button type="button" @click="urls.push('')">` styled as a subtle text link with inline + SVG icon.

**json_encode escaping:** Uses `htmlspecialchars(json_encode(...), ENT_QUOTES, 'UTF-8')` for safe embedding in HTML attributes — same pattern as access.tpl.php.

### 6. Action buttons and CSRF

The CSRF token and action buttons are placed together at the bottom of the form, before the closing `</div>` of the editor card:

```php
<?= \Idno\Core\Idno::site()->actions()->signForm('/profile/' . $vars['user']->getHandle()) ?>

<div style="display:flex;gap:0.5rem;margin-top:var(--spacing-section)">
    <button type="submit" class="idno-btn idno-btn-primary">
        <?= \Idno\Core\Idno::site()->language()->_('Save Changes') ?>
    </button>
    <a href="<?= $vars['user']->getDisplayURL() ?>" class="idno-btn idno-btn-ghost">
        <?= \Idno\Core\Idno::site()->language()->_('Cancel') ?>
    </a>
</div>
```

**Cancel:** An `<a>` link back to the profile page, not a JS `hideContentCreateForm()` call (which is a Bootstrap modal pattern we don't use).

### 7. Form attributes

- `action="<?= $vars['user']->getDisplayURL() ?>"` — same as default
- `method="post"` with `enctype="multipart/form-data"` (file upload)

**All user-visible strings** are wrapped in `\Idno\Core\Idno::site()->language()->_()` for i18n support, following the same pattern as the default template.

## Data Flow

1. PHP extracts user data from `$vars['user']` (name, description, icon URL, profile URLs)
2. PHP builds `$urls` array, ensuring at least one entry
3. Alpine.js manages avatar preview and URL list state client-side
4. On submit, standard form POST sends `name`, `profile[description]`, `profile[url][]`, and `avatar` file
5. No changes to the backend — same form field names as the default template

## What We're NOT Doing

- **No richtext editor for bio** — keeping it as a plain textarea, matching the default
- **No new CSS classes** — everything uses existing Twenty26 component styles
- **No jQuery** — Alpine.js replaces all jQuery functionality
- **No two-column layout** — single column, consistent with other modern edit templates
- **No tagline field** — it's commented out in the default template, we skip it entirely
