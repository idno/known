# Profile Edit Template Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create a modern template override for the profile edit page that replaces Bootstrap 3 with the Twenty26 design system.

**Architecture:** Single new template file using `idno-editor` card layout with Alpine.js for avatar preview and dynamic URL list. No CSS or JS module changes — all styling uses existing Twenty26 component classes.

**Tech Stack:** PHP (Bonita templates), Alpine.js v3, Twenty26 CSS design tokens

**Spec:** `docs/superpowers/specs/2026-03-12-profile-edit-template-design.md`

---

## Chunk 1: Template Implementation and Verification

### Task 1: Create directory and template file

**Files:**
- Create: `Themes/Twenty26/templates/modern/entity/User/edit.tpl.php`

This is the only file in the plan. No tests — this is a PHP template with no testable units. Verification is manual (browser check).

- [ ] **Step 1: Create the User directory**

```bash
mkdir -p Themes/Twenty26/templates/modern/entity/User
```

- [ ] **Step 2: Write the template file**

Create `Themes/Twenty26/templates/modern/entity/User/edit.tpl.php` with the full template. The file has these sections in order:

1. **PHP data preparation block** — builds `$urls` array from `$vars['user']->profile['url']`, normalizes to array, applies `$this->fixURL()`, ensures at least one empty entry
2. **PHP avatar icon escaping** — `$icon_url = htmlspecialchars(json_encode($vars['user']->getIcon()), ENT_QUOTES, 'UTF-8')`
3. **Form open** — `<form action method="post" enctype="multipart/form-data">`
4. **Editor card open** — `<div class="idno-editor">`
5. **Heading** — `<h4 class="idno-editor-heading">` with i18n
6. **Avatar section** — Alpine.js `x-data="{ preview: null, icon: ... }"` with `x-cloak`, circular `<img>` with `:src="preview || icon"`, hidden file input with `id="avatar"` `name="avatar"` `accept="image/*"`, `<label for="avatar">` styled as ghost button with camera SVG, FileReader `@change` handler, help text
7. **Name field** — `idno-form-field` with `idno-label` and `idno-input`, `name="name"`, value from `$vars['user']->getTitle()`
8. **Bio field** — `idno-form-field` with `idno-label` and `idno-textarea`, `name="profile[description]"`, value from `$vars['user']->getDescription()`
9. **Websites section** — Alpine.js `x-data="{ urls: ... }"` with `x-cloak`, `<template x-for>` loop with `x-model` inputs (`name="profile[url][]"`), remove buttons (hidden when `urls.length <= 1`), add button
10. **CSRF token** — `signForm('/profile/' . $vars['user']->getHandle())`
11. **Action buttons** — Submit "Save Changes" and Cancel `<a>` link to `$vars['user']->getDisplayURL()`
12. **Form + card close**

Key implementation details from the spec:

- Use `$vars['user']->getIcon()` (NOT `currentUser()->getIcon()`) — correct for admin editing another user
- Use `<?php echo ... ?>` long-form echo tags (matches codebase style, not `<?= ?>`)
- All user-visible strings wrapped in `\Idno\Core\Idno::site()->language()->_()` — including help text ("JPG, PNG or GIF"), placeholder text, and description text ("Other places on the web...")
- Alpine.js JSON escaping: `htmlspecialchars(json_encode(...), ENT_QUOTES, 'UTF-8')`
- `x-for` must use `<template>` wrapper (Alpine.js v3)
- URL inputs use `x-model` only (no `:value` — would conflict)
- Remove button uses `x-show="urls.length > 1"`
- Camera SVG icon (inline, Lucide-style): `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>`
- X (remove) SVG icon: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`
- Plus (add) SVG icon: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>`

- [ ] **Step 3: Rebuild theme CSS** (no CSS changes, but ensures dist is current)

```bash
cd Themes/Twenty26 && npm run build
```

Expected: Clean build, no errors. The `dist/modern.min.css` timestamp updates.

- [ ] **Step 4: Verify in browser**

1. Navigate to `https://idno:8890/profile/<username>/edit` (MAMP Pro local dev)
2. Hard-reload (`Cmd+Shift+R`) to bust cache
3. Verify:
   - Single-column card layout with "Edit your profile" heading
   - Avatar shows current user picture, circular, ~80px
   - "Change photo" button triggers file picker, preview updates
   - Name field pre-populated
   - Bio textarea pre-populated
   - Website URLs pre-populated (or one empty row)
   - Can add/remove website rows
   - "Save Changes" submits, "Cancel" links back to profile
4. Test form submission: change name, save, verify it persists
5. If admin account available: navigate to another user's profile edit, confirm avatar shows that user's icon (not admin's)

- [ ] **Step 5: Commit**

```bash
git add Themes/Twenty26/templates/modern/entity/User/edit.tpl.php
git commit -m "feat: add modern profile edit template for Twenty26 theme

Replace Bootstrap 3 two-column layout with single-column idno-editor
card. Avatar preview and dynamic URL list use Alpine.js instead of
jQuery. Uses $vars['user']->getIcon() (fixes default template's
incorrect currentUser()->getIcon() for admin editing)."
```
