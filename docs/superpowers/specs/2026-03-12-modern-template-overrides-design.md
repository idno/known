# Modern Template Overrides for User-Facing Pages

**Date:** 2026-03-12
**Status:** Draft
**Scope:** User-facing pages only (homepage feed, profile, single post, comments)

## Problem

The Twenty26 theme uses Tailwind CSS 4 and intentionally disables Bootstrap CSS (`shell/bootstrap.tpl.php` is empty). However, ~284 templates still fall back to their `default/` versions which contain Bootstrap 3 classes (`row`, `col-md-*`, `form-control`, `btn`, etc.). Without Bootstrap loaded, these render as unstyled, overlapping elements.

The most visible breakage on user-facing pages:
- **Content type filter bar** — unstyled text links at top of feed
- **Comment form** — overlapping avatar and textarea with broken grid layout
- **Annotation lists** (likes, shares, mentions, RSVPs) — broken Bootstrap grid with no styling
- **Edit/Delete links** — unstyled anchor tags
- **Syndication links** — unstyled POSSE links
- **Pagination** — unstyled pager controls
- **Annotation delete controls** — unstyled delete links inside annotation rows

## Template Resolution

Twenty26's `Controller.php` calls `setTemplateType('modern')`, which tells the Bonita template engine to look for templates in `templates/modern/` directories first. When no modern template exists, `fallbackToDefault` (true by default) causes it to load from `templates/default/` instead. The default Bootstrap templates remain untouched — all overrides are new files in `Themes/Twenty26/templates/modern/`.

## Approach

Create modern template overrides in `Themes/Twenty26/templates/modern/` for each affected template. Add a new CSS component file (`annotations.css`) using Tailwind 4's `@layer components` for annotation, comment, pagination, and content-type-bar styles, following the established pattern.

## Design Target

Visual polish comparable to Bluesky/Twitter — clean, minimal, consistent spacing, no visual noise.

## IndieWeb Microformat Preservation

All modern template overrides must preserve IndieWeb microformat classes from the original default templates. These include: `h-card`, `h-cite`, `h-entry`, `h-feed`, `u-url`, `u-photo`, `u-syndication`, `p-name`, `p-author`, `e-content`, `e-note`, `dt-published`, `rel="syndication"`, `rel="nofollow"`, `rel="in-reply-to"`. These are critical for IndieWeb interoperability (webmentions, microformat parsers, etc.).

## CSS Architecture

### New file: `src/css/components/annotations.css`

All styles in `@layer components` so plugin developers can override at the same specificity.

**New classes:**

```
.idno-annotation            — Flex row for a single annotation (like, share, mention, rsvp)
.idno-annotation-avatar     — Small avatar (1.5rem) in annotation rows
.idno-annotation-content    — Text content beside avatar
.idno-annotation-section    — Section header for grouped annotations (e.g., "Attending")
.idno-annotation-delete     — Subtle delete control for authorized annotation removal
.idno-comment-form          — Comment form container (flex layout, avatar + form)
.idno-comment-form-actions  — Submit button alignment container in comment form
.idno-content-type-bar      — Horizontal pill/tab bar for content type filters
.idno-content-type-tab      — Individual tab/pill in the filter bar
.idno-content-type-tab.active — Active state for selected tab
.idno-syndication-links     — Container for POSSE/syndication links
.idno-pagination            — Pagination container (newer/older nav)
.idno-pagination a          — Pagination link styling
.idno-pagination .pagination-disabled a — Disabled state for pagination
```

**Design tokens used:** All existing tokens from `tokens.css` — `--color-*`, `--spacing-*`, `--radius-*`, `--font-size-*`, `--shadow-*`. No new tokens needed.

### Update: `src/css/main.css`

Add `@import "./components/annotations.css";` to the import list.

### Tailwind 4 `@source` directives

Already configured in `main.css`:
```css
@source "../../templates/";
@source "../../../../IdnoPlugins/*/templates/modern/";
```

These scan the template directories for Tailwind utility classes used directly in templates, so any utilities used in the new templates will be included in the build automatically.

## Existing Modern Templates (Context)

These templates already exist and are load-bearing — the new templates plug into the call chains initiated by these files:

- **`content/end.tpl.php`** — The main post footer on permalink pages. Calls `content/end/annotations`, `entity/annotations/comment/main`, `entity/annotations/comment/mini`, and `content/syndication/links`. All of these are targets of this spec.
- **`content/feed/end.tpl.php`** — The compact post footer in feed cards. Also calls `content/syndication/links`. The new syndication links template must work in both permalink and feed contexts.
- **`entity/annotations/replies.tpl.php`** — Already exists as a modern override. Uses `idno-entry` and `idno-entry-*` classes with inline styles. For consistency, this file should be updated to use the new `idno-annotation` classes introduced by this spec, but that update can be done alongside or after the new templates.
- **`entity/shell.tpl.php`** — The post container. Calls `content/end` in its footer.
- **`entity/User.tpl.php`** — The profile card. Already has a modern override.

## Template Overrides

All files created under `Themes/Twenty26/templates/modern/`.

### 1. `content/create.tpl.php`

**Purpose:** Content type filter bar at top of homepage feed.
**Current default:** Uses `buttonBar`, `row`, `col-md-12`, Bootstrap grid. Links call `contentCreateForm()` JS function to open inline edit forms.
**Modern design:** Horizontal tab bar with pill-style tabs. Since Twenty26 uses a compose modal (already built via `shell/compose.tpl.php`), this becomes purely a feed filter — not a creation form launcher.

Each content type links to `$contentType->getEditURL()` as a direct navigation link (no JavaScript interception). This takes users to the dedicated edit page for that content type, which is the standard Idno pattern. The `contentCreateForm()` inline approach and `#contentCreate` div are dropped — the compose modal handles inline creation. The content type icons from `$contentType->getIcon()` are preserved.

Structure:
```html
<div class="idno-content-type-bar">
  <!-- Loop through $vars['contentTypes'] -->
  <a class="idno-content-type-tab" href="<?= $contentType->getEditURL() ?>">
    <span>Type Icon</span> Type Name
  </a>
</div>
```

### 2. `entity/annotations/comment/main.tpl.php`

**Purpose:** Comment input form on permalink pages.
**Current default:** Uses `row`, `col-md-2`, `col-md-10`, `form-control`, `btn btn-save`.
**Modern design:** Compact inline form. Avatar on left, textarea + submit button in a clean column. Uses component classes only (no inline styles).

Structure:
```html
<div class="idno-comment-form">
  <img class="idno-annotation-avatar u-photo" ... />
  <form ...>
    <textarea class="idno-textarea" placeholder="Add a comment..." ...></textarea>
    <!-- hidden fields: CSRF token, object UUID, type=reply -->
    <div class="idno-comment-form-actions">
      <button class="idno-btn idno-btn-primary idno-btn-sm" type="submit">Comment</button>
    </div>
  </form>
</div>
```

### 3. `entity/annotations/likes.tpl.php`

**Purpose:** List of users who liked a post.
**Current default:** Uses `idno-annotation row`, `col-md-1 hidden-sm`, `col-md-6`.
**Modern design:** Compact flex rows. Small avatar, "Name liked this post", date + source.

Structure per annotation:
```html
<div class="idno-annotation">
  <!-- avatar via entity/annotations/image draw -->
  <img class="idno-annotation-avatar" ... />
  <div class="idno-annotation-content">
    <span><a href="..." rel="nofollow">Name</a> liked this post</span>
    <span class="idno-entry-meta">
      <a href="..." rel="nofollow">Mar 12, 2026</a> on
      <a href="..." rel="nofollow">domain.com</a>
    </span>
  </div>
  <!-- annotation delete control if authorized -->
</div>
```

### 4. `entity/annotations/shares.tpl.php`

**Purpose:** List of reshares.
**Current default:** Same Bootstrap pattern as likes.
**Modern design:** Same annotation row pattern as likes, with "reshared this post" text.

### 5. `entity/annotations/mentions.tpl.php`

**Purpose:** List of webmentions/mentions.
**Current default:** Same Bootstrap pattern.
**Modern design:** Same annotation row pattern. "Mentioned in: [title or URL]" with author and date.

### 6. `entity/annotations/rsvps.tpl.php`

**Purpose:** RSVP responses for events, grouped by yes/maybe/no/other.
**Current default:** Uses Bootstrap grid with grouped sections.
**Modern design:** Section headers (`.idno-annotation-section`) for each response type, with annotation rows underneath.

Structure:
```html
<div class="idno-annotation-section">
  <h4>Attending</h4>
</div>
<div class="idno-annotation">...</div>
<!-- more annotation rows -->

<div class="idno-annotation-section">
  <h4>Maybe attending</h4>
</div>
<!-- etc. -->
```

### 7. `content/annotation/edit.tpl.php`

**Purpose:** Delete control for individual annotations. Called by likes, shares, mentions, rsvps, and replies templates via `$this->draw('content/annotation/edit')`.
**Current default:** Uses `edit edit-annotation` classes on a div with a bare `<p>` tag.
**Modern design:** Subtle inline delete link using `.idno-annotation-delete`, consistent with the annotation row layout.

Structure:
```html
<span class="idno-annotation-delete">
  <!-- createLink for annotation delete with POST method -->
</span>
```

### 8. `content/end/annotations.tpl.php`

**Purpose:** Orchestrator that iterates annotation types and renders each.
**Current default:** Simple loop, no Bootstrap classes.
**Modern design:** Same logic, wrapped in a container for spacing control.

```html
<div class="idno-annotations-list">
  <!-- renders rsvps, likes, shares, replies, mentions in order -->
</div>
```

### 9. `content/edit.tpl.php`

**Purpose:** Edit/Delete links on posts.
**Current default:** Plain `<a class="edit">` tags. Also calls `$this->draw('content/entity/' . $vars['object']->getEntityTypeName() . '/edit')` to allow plugins to inject type-specific edit controls.
**Modern design:** Use existing `idno-entry-action` class for consistency with the post footer actions. The plugin sub-template call must be preserved so plugins can inject their own edit controls.

### 10. `entity/feed.tpl.php`

**Purpose:** Iterates feed items and draws pagination.
**Current default:** Clean loop, calls `drawPagination()` which renders `shell/pagination.tpl.php`.
**Modern design:** Same loop logic.

### 11. `shell/pagination.tpl.php`

**Purpose:** Newer/Older pagination controls.
**Current default:** Uses `pager`, `newer`, `older`, `pagination-disabled` classes with `<ul><li>` structure.
**Modern design:** Same logic, wrapped in `.idno-pagination` container. Preserves the existing class names (`pager`, `newer`, `older`, `pagination-disabled`) since the CSS will target these within `.idno-pagination`. The data attributes for XHR pagination are preserved.

### 12. `content/syndication/links.tpl.php`

**Purpose:** "Also on" POSSE links (showing where content was syndicated).
**Current default:** Uses `.posse` class. Renders service icons via `content/syndication/icon/` sub-templates.
**Modern design:** Styled as subtle metadata using `.idno-syndication-links`. Preserves `u-syndication` and `rel="syndication"` microformat attributes. Must work in both permalink (full footer via `content/end.tpl.php`) and feed card (compact footer via `content/feed/end.tpl.php`) contexts.

Note: `rel="syndication"` is only added on permalink pages (behind an `isPermalink()` check), following IndieWeb conventions. In feed card contexts, the links are rendered without `rel="syndication"`.

Structure:
```html
<div class="idno-syndication-links">
  <span class="idno-entry-meta">Also on:</span>
  <a href="..." class="idno-entry-meta u-syndication service-name"
     <?php if (isPermalink) ?>rel="syndication"<?php endif ?>>
    icon identifier
  </a>
</div>
```

### 13. `pages/home.tpl.php`

**Purpose:** Homepage template — renders filter bar + feed.
**Current default:** Minimal — calls `content/create` and `entity/feed`. Also conditionally draws `robot/wizard` for users with `robot_state` set.
**Modern design:** Same structure, ensuring the compose modal is triggered from nav (already handled) rather than from the old inline creation bar. The `robot/wizard` draw is omitted — it is a legacy onboarding feature not used in the modern theme.

**Intentionally omitted: `entity/annotations/comment/mini.tpl.php`** — The modern `content/end.tpl.php` calls this for non-permalink views when logged in. There is no core default template for this (only empty files in some legacy themes), so the draw silently renders nothing. This is intentional for now — inline commenting on feed cards is not part of the initial modern UI. A mini comment form can be added later as a separate feature if desired.

## Consistency Update: `entity/annotations/replies.tpl.php`

The existing modern `replies.tpl.php` uses `idno-entry` classes with inline styles rather than the `idno-annotation` pattern. For visual consistency across all annotation types, this file should be updated to use the new annotation classes. This can be done alongside or after the new templates — it is not blocking.

## Build

After creating templates and CSS:
```bash
cd Themes/Twenty26 && npm run build
```

This runs Vite which processes Tailwind 4 via PostCSS, scanning template directories for utility classes via `@source` directives.

## File List

New files to create:
```
Themes/Twenty26/src/css/components/annotations.css
Themes/Twenty26/templates/modern/content/create.tpl.php
Themes/Twenty26/templates/modern/content/annotation/edit.tpl.php
Themes/Twenty26/templates/modern/content/edit.tpl.php
Themes/Twenty26/templates/modern/content/end/annotations.tpl.php
Themes/Twenty26/templates/modern/content/syndication/links.tpl.php
Themes/Twenty26/templates/modern/entity/annotations/comment/main.tpl.php
Themes/Twenty26/templates/modern/entity/annotations/likes.tpl.php
Themes/Twenty26/templates/modern/entity/annotations/shares.tpl.php
Themes/Twenty26/templates/modern/entity/annotations/mentions.tpl.php
Themes/Twenty26/templates/modern/entity/annotations/rsvps.tpl.php
Themes/Twenty26/templates/modern/entity/feed.tpl.php
Themes/Twenty26/templates/modern/shell/pagination.tpl.php
Themes/Twenty26/templates/modern/pages/home.tpl.php
```

Files to modify:
```
Themes/Twenty26/src/css/main.css                              (add annotations.css import)
Themes/Twenty26/templates/modern/entity/annotations/replies.tpl.php  (consistency update)
```

## Testing

After building, verify each page type:
1. Homepage (`/`) — filter bar styled, feed renders correctly, pagination works
2. Profile (`/profile/ben`) — no overlapping elements, comment form clean
3. Single post (click any entry) — annotations section styled, comment form works, edit/delete links visible
4. Post with annotations — likes/shares/mentions display in clean rows, delete controls visible for authorized users
5. Post with syndication links — "Also on" links display cleanly on both permalink and feed views

## Future Work

- Admin page template overrides (Phase 2)
- Account settings template overrides (Phase 2)
- Login/register page overrides (Phase 3)
- Form input component overrides (Phase 3)
- Plugin admin page overrides (Phase 4)
