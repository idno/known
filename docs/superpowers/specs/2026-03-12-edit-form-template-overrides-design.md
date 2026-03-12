# Edit Form Template Overrides — Design Spec

## Goal

Replace Bootstrap-dependent shared form infrastructure templates with modern overrides using existing `idno-*` CSS classes and inline SVG icons, so that edit forms render correctly in the Twenty26 theme without Bootstrap CSS.

## Problem

The modern plugin edit templates (Status, Entry, Photo, Checkin) already use `idno-*` classes. But they call shared infrastructure templates that still emit Bootstrap classes: `form-control`, `btn-group`, `dropdown-toggle`, `fa fa-*`. Without Bootstrap CSS loaded, these render as unstyled elements or raw text.

## Architecture

Create 5 modern template overrides in `Themes/Twenty26/templates/modern/`. Bonita's template resolution (`setTemplateType('modern')`) checks `templates/modern/` first and falls back to `templates/default/`. This means we override only the broken shared templates without touching the defaults.

New CSS is minimal: one `.idno-access-dropdown` component in `forms.css`. Everything else uses existing `idno-input`, `idno-textarea`, `idno-select`, `idno-btn`, and `idno-btn-ghost` classes.

## Decisions

- **Access control dropdown**: Alpine.js replacing Bootstrap `btn-group` + `dropdown-toggle`. Alpine.js is already loaded in the Twenty26 theme.
- **Icons**: Inline SVGs everywhere (globe, users, lock, camera, trash, chevron, cog). No Font Awesome dependency.
- **No changes to**: plugin edit templates (already modernized), Tiptap editor (already has modern override), `forms/input/hidden.tpl.php` (no visual output), `entity/edit/header.tpl.php` / `footer.tpl.php` (clean enough, no Bootstrap).

---

## Template 1: `content/access.tpl.php`

**Path:** `Themes/Twenty26/templates/modern/content/access.tpl.php`
**Replaces:** `templates/default/content/access.tpl.php`

### What the default does

- Reads `$vars['object']->access` or `$vars['default-access']` to determine current access level
- Generates a unique `$id_code` for the hidden input
- If `show_privacy` config is set or access is not PUBLIC, renders a Bootstrap `btn-group` dropdown with Font Awesome icons (`fa-globe`, `fa-group`, `fa-lock`)
- Clicking a dropdown option runs JS to update the hidden input `access-control-id-{id_code}`
- Supports custom `AccessGroup` entities from `\Idno\Entities\AccessGroup::get()`
- If privacy is hidden and access is PUBLIC, renders only the hidden input
- Calls `$this->documentFormControl()` for API documentation

### What the override does

- Keeps all PHP logic identical: `$access` resolution, `$id_code` generation, `show_privacy` check, `AccessGroup::get()`
- `documentFormControl()` remains **outside** the `if/else` conditional, exactly as the default — it always runs regardless of whether the dropdown is rendered
- Wraps the dropdown in `<div class="access-control-block">` to preserve the existing wrapper class (may be referenced by JS/CSS elsewhere)
- Replaces Bootstrap `btn-group` + `dropdown-toggle` + `dropdown-menu` with an Alpine.js component using `x-data`, `x-show`, `@click.away`
- **Alpine.js fully replaces** the existing `.acl-ctrl-option` / `data-acl` JS event bindings. The Alpine click handler directly sets `document.getElementById('access-control-id-{id_code}').value` when an option is selected. No need to preserve `acl-ctrl-option` class or `data-acl` attributes.
- Replaces Font Awesome icons with inline SVGs:
  - `fa-globe` → globe SVG (circle + meridian lines)
  - `fa-group` → users SVG (two person silhouettes)
  - `fa-lock` → lock SVG (padlock)
  - `fa-cog` → cog/settings SVG (for custom access groups)
  - `fa-users` → users SVG (for FOLLOWING type access groups)
- Trigger button uses `idno-access-trigger` class
- Menu panel uses `idno-access-menu` class, pops upward
- Each option uses `idno-access-option` class with checkmark SVG for active state
- Alpine.js state manages: `open` (boolean), `selected` (access value), `label` (display text), `icon` (which SVG to show)
- **Initial state derived from PHP `$access`**: PHP maps the access value to the correct initial label and icon:
  - `PUBLIC` → label "Public", globe icon
  - `SITE` → label "Members only", users icon
  - Current user UUID → label "Private", lock icon
  - Custom AccessGroup UUID → label from `$acl->title`, cog icon (or users icon for FOLLOWING type)
  - This fixes a bug in the default where the trigger always shows "Public" regardless of actual access value
- Clicking an option updates the hidden input value, updates the trigger label/icon, and closes the menu
- Chevron SVG on the trigger button

### New CSS needed

```css
.idno-access-dropdown { position: relative; display: inline-block; }

.idno-access-trigger {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.625rem;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  background: var(--color-surface);
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
  cursor: pointer;
  transition: border-color 0.15s;
}
.idno-access-trigger:hover {
  border-color: var(--color-text-muted);
  color: var(--color-text);
}
.idno-access-trigger:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}
.idno-access-trigger svg { width: 14px; height: 14px; }
.idno-access-trigger .chevron { width: 10px; height: 10px; opacity: 0.4; }

.idno-access-menu {
  position: absolute;
  bottom: calc(100% + 4px);
  left: 0;
  min-width: 200px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  box-shadow: var(--shadow-md);
  padding: 0.25rem;
  z-index: 50;
}

.idno-access-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.625rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: var(--font-size-sm);
  color: var(--color-text);
  border: none;
  background: none;
  width: 100%;
  text-align: left;
}
.idno-access-option:hover { background: var(--color-bg); }
.idno-access-option:focus-visible { background: var(--color-bg); outline: none; }
.idno-access-option.active {
  background: var(--color-bg);
  font-weight: 600;
}
.idno-access-option svg { width: 16px; height: 16px; color: var(--color-text-secondary); flex-shrink: 0; }
.idno-access-option .check { width: 14px; height: 14px; margin-left: auto; color: var(--color-primary); }
```

---

## Template 2: `forms/input/longtext.tpl.php`

**Path:** `Themes/Twenty26/templates/modern/forms/input/longtext.tpl.php`
**Replaces:** `templates/default/forms/input/longtext.tpl.php`

### What the default does

- Resolves variables: `$unique_id`, `$class`, `$height`, `$placeholder`, `$value`, `$required`
- Renders a `<br class="clearall">` followed by a `<textarea>` with classes `bodyInput mentionable form-control {$class}`
- Sets inline `style="height:{$height}px"`
- Calls `$this->documentFormControl()` for API documentation
- Unsets vars to prevent Bonita leakage

### What the override does

- Keeps all PHP variable resolution identical
- Drops the `<br class="clearall">`
- Replaces class `form-control` with `idno-textarea`
- Final class string: `bodyInput mentionable idno-textarea {$class}`
- Keeps all attributes: name, placeholder, style (height), id, required
- Keeps `documentFormControl()` call (note: the default passes `$name` not `$vars['name']` — this relies on Bonita variable extraction; replicate as-is) and var cleanup

### No new CSS needed

`idno-textarea` already exists in `forms.css`.

---

## Template 3: `forms/input/input.tpl.php`

**Path:** `Themes/Twenty26/templates/modern/forms/input/input.tpl.php`
**Replaces:** `templates/default/forms/input/input.tpl.php`

### What the default does

- Defines `$fields_and_defaults` array with all HTML input attributes
- Auto-generates unique `id` if not set
- Iterates `$fields_and_defaults` to emit HTML attributes
- Sets class to `form-control input {$vars['class']}` (or `form-control input input-{type}` as fallback)
- Handles `placeholder` and `alt` as special cases (htmlentities encoding)
- Emits `value` attribute
- Calls `$this->documentFormControl()` for API documentation
- Unsets vars to prevent Bonita pollution

### What the override does

- Keeps all PHP logic identical: `$fields_and_defaults`, id generation, attribute loop, placeholder/alt handling, value, documentFormControl, var cleanup
- Replaces class `form-control input` with `idno-input`
- Final class string: `idno-input {$vars['class']}` (or `idno-input input-{type}` as fallback)

### No new CSS needed

`idno-input` already exists in `forms.css`.

---

## Template 4: `forms/input/select.tpl.php`

**Path:** `Themes/Twenty26/templates/modern/forms/input/select.tpl.php`
**Replaces:** `templates/default/forms/input/select.tpl.php`

### What the default does

- Defines `$fields_and_defaults` array (same as input.tpl.php)
- Handles `blank-default` (empty first option) and `multiple` support
- Auto-generates unique `id` if not set
- Appends `[]` to name and `select-multiple` to class for multiple selects
- Handles `$vars['value']` as array for multi-select
- Iterates `$fields_and_defaults` to emit HTML attributes
- Sets class to `input {$vars['class']}` (or `input input-select` as fallback)
- Iterates `$vars['options']` to emit `<option>` elements with selected state
- Calls `$this->documentFormControl()` for API documentation
- Unsets vars to prevent Bonita pollution

### What the override does

- Keeps all PHP logic identical: `$fields_and_defaults`, blank-default, multiple handling, id generation, attribute loop, options iteration, documentFormControl, var cleanup
- Replaces class `input` with `idno-select`
- Final class string: `idno-select {$vars['class']}` (or just `idno-select` as fallback — drop the Bootstrap-era `input-select` fallback)

### No new CSS needed

`idno-select` already exists in `forms.css`.

---

## Template 5: `forms/input/image-file.tpl.php`

**Path:** `Themes/Twenty26/templates/modern/forms/input/image-file.tpl.php`
**Replaces:** `templates/default/forms/input/image-file.tpl.php`

### What the default does

- Generates unique `$vars['id']` if empty
- Detects `$multiple` from `[]` in name
- Detects `$hide_existing` from vars
- For existing objects with attachments:
  - Iterates attachments, resolves thumbnail URLs (large → thumbnail_large → thumbnail → original)
  - Patches broken URLs from historical bug (#526)
  - Sanitizes attachment URLs
  - Shows existing photo with `<img>` tag
  - If user can edit and hide-delete is not set: renders delete link using `createLink()` with `fa fa-trash-o` icon, POST method, confirm dialog
- Shows preview area (`photo-preview`) with hidden `<img>` for JS preview
- Renders upload button: `<span class="btn btn-primary btn-file">` wrapping `fa fa-camera` icon + label text + hidden file input
- Label text changes based on context: "Select a photo" / "Choose different photo" / "Add photo"
- File input calls `forms/input/file` which delegates to `forms/input/input`

### What the override does

- Keeps all PHP logic identical: id generation, multiple detection, hide-existing, attachment iteration, URL resolution, URL patching, URL sanitization, createLink for delete, preview area, file input delegation
- Replaces the `<span class="btn btn-primary btn-file">` wrapper with a `<label class="idno-btn idno-btn-ghost">` (full-width, centered). The `<label>` wraps the hidden file input directly, so clicking it opens the file picker without needing the Bootstrap `btn-file` CSS trick (which positioned a transparent file input over the button). The file input gets `style="display:none"` and the label's `for` attribute targets the input's id.
- Updates the class passed to the nested file input: replaces `'input-file form-control col-md-9'` with `'input-file'` to remove Bootstrap classes that would leak through
- Replaces `fa fa-camera` with inline camera SVG (viewBox 0 0 24 24, camera body + lens circle)
- Replaces `fa fa-trash-o` in delete link with inline trash SVG (viewBox 0 0 24 24, lid + can body)
- Delete button positioned as overlay on existing photo (absolute positioning, dark semi-transparent background, white icon, red on hover)
- Existing photo wrapped in `idno-image-existing` container for relative positioning
- Preview area kept functionally identical for `Template.activateImagePreview()`

### New CSS needed

```css
.idno-image-existing {
  position: relative;
  margin-bottom: 0.75rem;
}
.idno-image-existing img {
  width: 100%;
  border-radius: var(--radius-sm);
  display: block;
}
.idno-image-delete {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 2rem;
  height: 2rem;
  border-radius: var(--radius-sm);
  background: rgba(0,0,0,0.6);
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
.idno-image-delete:hover {
  background: rgba(220,38,38,0.8);
}
.idno-image-delete:focus-visible {
  outline: 2px solid white;
  outline-offset: 2px;
}
.idno-image-delete svg {
  width: 14px;
  height: 14px;
}
```

---

## What we're NOT changing

- **Plugin edit templates** (Status, Entry, Photo, Checkin) — already use `idno-*` classes
- **Tiptap rich text editor** — already has a modern override at `forms/input/richtext.tpl.php`
- **`forms/input/hidden.tpl.php`** — no visual output
- **`entity/edit/header.tpl.php`**, **`entity/edit/footer.tpl.php`** — clean markup, no Bootstrap
- **`forms/input/file.tpl.php`** — delegates to `forms/input/input` which we are overriding; the file input itself needs no visual changes

## CSS Changes Summary

| File | Change |
|------|--------|
| `forms.css` | Add `.idno-access-dropdown`, `.idno-access-trigger`, `.idno-access-menu`, `.idno-access-option` classes |
| `forms.css` | Add `.idno-image-existing`, `.idno-image-delete` classes |

No other CSS files need changes. All form input classes (`idno-input`, `idno-textarea`, `idno-select`, `idno-btn`, `idno-btn-ghost`) already exist.

## Build

After creating/modifying files, rebuild the theme:

```bash
cd Themes/Twenty26 && npm run build
```

This compiles `src/css/main.css` → `dist/modern.min.css` and `src/js/main.js` → `dist/modern.min.js`.
