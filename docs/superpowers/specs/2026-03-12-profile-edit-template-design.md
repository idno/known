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
    <h4 class="idno-editor-heading">Edit your profile</h4>
    <!-- fields -->
</div>
```

No description paragraph — the heading is self-explanatory.

### 2. Avatar section (Alpine.js)

Horizontal layout: circular avatar preview on the left, "Change photo" label-button on the right.

**Alpine component:** `x-data="{ preview: null }"`
- `preview` — data URL from FileReader, or `null` to show current avatar
- On file input `change`: read the selected file with `FileReader.readAsDataURL()`, set `preview` to the result
- Avatar `<img>` uses `:src="preview || '<?= ...currentIcon ?>'"`

**File input hiding:** Uses `<label for="avatar">` styled as `idno-btn idno-btn-ghost` with an inline camera SVG icon. The actual `<input type="file">` is hidden with `display:none`.

**Help text:** `<p class="idno-form-help">` below the button.

### 3. Name field

Standard form field:
```php
<div class="idno-form-field">
    <label class="idno-label" for="name">Your name</label>
    <input class="idno-input" type="text" id="name" name="name" value="...">
</div>
```

### 4. Bio / About you field

Plain textarea (not richtext — the default template uses a plain textarea, and profile descriptions are typically short):

```php
<div class="idno-form-field">
    <label class="idno-label" for="body">About you</label>
    <textarea class="idno-textarea" name="profile[description]" id="body"
              placeholder="Tell people about yourself...">...</textarea>
</div>
```

### 5. Websites section (Alpine.js)

Dynamic URL list managed by Alpine.js.

**Alpine component:** Wraps the websites section.
```
x-data="{ urls: <?= htmlspecialchars(json_encode($urls), ENT_QUOTES, 'UTF-8') ?> }"
```

Where `$urls` is built in PHP from `$vars['user']->profile['url']`, always ensuring at least one empty string entry so there's always one visible input.

**Each URL row:** Rendered with `x-for="(url, index) in urls"`:
- `<input type="url" name="profile[url][]" :value="url" x-model="urls[index]">` with class `idno-input`
- Remove button: `<button @click="urls.splice(index, 1)">` with inline X SVG, only shown when `urls.length > 1` (always keep at least one row)

**Add button:** `<button @click="urls.push('')">` styled as a subtle text link with inline + SVG icon.

**json_encode escaping:** Uses `htmlspecialchars(json_encode(...), ENT_QUOTES, 'UTF-8')` for safe embedding in HTML attributes — same pattern as access.tpl.php.

### 6. Action buttons

```php
<div style="display:flex;gap:0.5rem;margin-top:var(--spacing-section)">
    <button type="submit" class="idno-btn idno-btn-primary">Save Changes</button>
    <a href="<?= $vars['user']->getDisplayURL() ?>" class="idno-btn idno-btn-ghost">Cancel</a>
</div>
```

**Cancel:** An `<a>` link back to the profile page, not a JS `hideContentCreateForm()` call (which is a Bootstrap modal pattern we don't use).

### 7. Form attributes and security

- `action="<?= $vars['user']->getDisplayURL() ?>"` — same as default
- `method="post"` with `enctype="multipart/form-data"` (file upload)
- `<?= \Idno\Core\Idno::site()->actions()->signForm(...)?>` — CSRF token

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
