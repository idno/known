# Idno 2026 Theme — Design Specification

## Overview

Replace Idno's Bootstrap 3 frontend with a modern, polished UI built on Tailwind CSS. The new look ships as a theme called "Idno 2026" under `Themes/2026/`, using the existing theme override system. The admin panel also gets the Tailwind treatment. Existing themes and plugin templates continue to load Bootstrap as they always have — no shim, no emulation.

This theme is designed as the future default. It introduces a `modern` template type that coexists with `default`, with automatic fallback. When the time comes, promoting it to core means making `modern` the primary template type and moving plugin templates into their respective plugin directories.

## Architecture

### Approach: New `modern` template type + theme

The theme introduces a new template type called `modern` alongside the existing `default`, `json`, `activitypub`, etc. Template types in Idno control which set of templates is used for rendering, with automatic fallback to `default` when a specific template doesn't exist in the active type.

**How it works:**

1. The theme's `Controller.php` calls `setTemplateType('modern')` during initialization
2. All theme templates live under `templates/modern/` (not `templates/default/`)
3. Bundled plugins (Text, Status, Photo, etc.) each get `templates/modern/` directories with Tailwind-styled edit and display templates
4. If a plugin hasn't been updated (e.g., a third-party plugin), the template resolver automatically falls back to `templates/default/` — the old Bootstrap template renders. It won't look perfect but remains functional.
5. Plugins migrate to `modern` at their own pace by adding a `templates/modern/` directory

This is the same mechanism used by `json`, `activitypub`, `email`, etc. — `templateTypeExists()` validates the type exists, `draw()` searches the active type first and falls back to `default`. No new core rendering code needed.

**Core changes (minimal):**

- `ContentType::getDescription()` — short description for content type pickers
- Plugin base class `getAdminIcon()` — Lucide icon name for admin navigation
- UI exposure of the existing `publish_status` draft system
- `templates/modern/` directory in core (can be empty/`.gitkeep`) to register the template type. Alternatively, the theme's own `templates/modern/` is sufficient since `templateTypeExists()` checks all registered paths.

**Asset loading:**

- **New theme (`modern` type)**: Shell templates load Tailwind CSS, Alpine.js, Tiptap. No Bootstrap, no jQuery.
- **Old themes/plugins (`default` type)**: Load Bootstrap CSS/JS as they always have. No Tailwind.
- **Fallback templates**: When a `default` template renders inside the `modern` shell, it gets the modern CSS context. Bootstrap-specific classes won't be styled, but basic HTML form elements still work. This is acceptable graceful degradation.

### Theme directory structure

```
Themes/2026/
├── theme.ini                      # Template replacements and extensions
├── Controller.php                 # Theme initialization — sets template type to 'modern'
├── preview.png                    # Theme selector preview image
├── package.json                   # Node dependencies
├── vite.config.js                 # Vite build configuration
├── postcss.config.js              # PostCSS configuration (Tailwind v4 plugin)
├── src/
│   ├── css/
│   │   ├── main.css               # Entry point: @import "tailwindcss", tokens, components
│   │   ├── tokens.css             # @theme layer: design tokens (colors, spacing, radii, typography)
│   │   └── components/            # @layer components: semantic Idno classes
│   │       ├── layout.css         # .idno-shell, .idno-grid, .idno-container
│   │       ├── navigation.css     # .idno-nav, .idno-nav-item, .idno-nav-icon
│   │       ├── post-card.css      # .idno-entry, .idno-entry-header, .idno-entry-body
│   │       ├── forms.css          # .idno-input, .idno-select, .idno-textarea
│   │       ├── buttons.css        # .idno-btn, .idno-btn-primary, .idno-btn-ghost
│   │       ├── modal.css          # .idno-modal, .idno-modal-header, .idno-modal-body
│   │       ├── editor.css         # .idno-editor, .idno-toolbar
│   │       ├── profile.css        # .idno-profile, .idno-profile-header
│   │       └── admin.css          # .idno-admin-shell, .idno-admin-sidebar, .idno-admin-content
│   └── js/
│       ├── main.js                # Alpine.js init + module imports
│       └── modules/
│           ├── mentions.js        # @-mention autocomplete
│           ├── interactions.js    # Like, reply, repost actions
│           ├── media.js           # Image upload, file handling
│           ├── editor.js          # Tiptap initialization and config
│           ├── compose.js         # New post modal + content type picker
│           └── autosave.js        # Form autosave logic
├── dist/                          # Vite build output (committed)
│   ├── modern.min.css
│   ├── modern.min.js
│   └── modern.min.js.map
├── templates/
│   └── modern/                    # 'modern' template type — fallback to 'default' is automatic
│       ├── shell.tpl.php          # Full page shell (Tailwind + Alpine)
│       ├── shell/
│       │   ├── bootstrap.tpl.php  # Empty file — prevents Bootstrap loading
│       │   ├── css.tpl.php        # Loads modern.min.css
│       │   ├── javascript.tpl.php # Loads Alpine.js, no jQuery
│       │   ├── head.tpl.php       # Modern meta, font loading
│       │   └── footerjavascript.tpl.php  # Loads modern.min.js, no TinyMCE
│       ├── entity/
│       │   ├── shell.tpl.php      # Entry wrapper with h-entry + Tailwind
│       │   ├── User.tpl.php       # Profile page with h-card
│       │   ├── FeedItem.tpl.php   # Feed item variant
│       │   └── annotations/
│       │       └── replies.tpl.php  # Comments with h-cite
│       ├── content/
│       │   ├── end.tpl.php        # Entry footer (interactions, syndication)
│       │   └── feed/
│       │       └── end.tpl.php    # Feed item footer
│       ├── forms/
│       │   └── input/
│       │       └── richtext.tpl.php  # Tiptap editor (replaces TinyMCE)
│       ├── drafts.tpl.php           # User's draft entries list
│       └── admin/
│           ├── shell.tpl.php      # Admin shell with dark sidebar
│           ├── menu.tpl.php       # Admin nav with Lucide icons
│           ├── home.tpl.php       # Dashboard
│           ├── themes.tpl.php     # Theme management
│           ├── plugins.tpl.php    # Plugin management
│           ├── users.tpl.php      # User management
│           ├── email.tpl.php      # Email settings
│           ├── statistics.tpl.php # Statistics
│           ├── logs.tpl.php       # Log viewer
│           ├── import.tpl.php     # Import
│           └── export.tpl.php     # Export
└── LICENSE-lucide.txt             # Lucide icons ISC license
```

### Plugin template structure (bundled plugins)

Each bundled plugin gets a `templates/modern/` directory alongside its existing `templates/default/`:

```
IdnoPlugins/Text/
├── templates/
│   ├── default/                   # Existing Bootstrap templates (unchanged)
│   │   └── entity/Entry/
│   │       ├── edit.tpl.php
│   │       └── ...
│   └── modern/                    # New Tailwind templates
│       └── entity/Entry/
│           ├── edit.tpl.php       # Tiptap-based article editor
│           └── ...

IdnoPlugins/Status/
├── templates/
│   ├── default/
│   │   └── entity/Idno/Status/
│   │       ├── edit.tpl.php
│   │       └── ...
│   └── modern/
│       └── entity/Idno/Status/
│           ├── edit.tpl.php       # Modern status compose form
│           └── ...

# Same pattern for Photo, Event, Checkin, Like, etc.
```

Third-party plugins that don't provide `templates/modern/` automatically fall back to their `templates/default/` templates — functional but Bootstrap-styled.

## Build System

### Vite (new theme)

Vite handles the new theme's assets inside `Themes/2026/`:

- **Input**: `src/css/main.css` and `src/js/main.js`
- **Output**: `dist/modern.min.css` and `dist/modern.min.js`
- **Tailwind** scans `Themes/2026/templates/` and `IdnoPlugins/*/templates/modern/` for class usage, purging unused utilities
- **Development**: `cd Themes/2026 && npm run dev` (Vite watch mode)
- **Production**: `npm run build` outputs to `dist/`, which is committed to the repo so the theme works without Node.js in production. The project `.gitignore` must not exclude `Themes/2026/dist/`.

### Grunt (legacy — untouched)

Grunt continues to build `css/idno.css` and `js/idno.min.js` for Bootstrap-based themes. No changes to `Gruntfile.js`. The two build systems are fully independent.

## CSS Architecture

### Three-layer design system (Tailwind v4)

The CSS architecture uses Tailwind v4's native `@theme`, `@layer`, and cascade layer features to create a proper design system with a stable API for plugin and theme developers. No `@apply` — component classes use plain CSS referencing design tokens.

#### Layer 1: Design Tokens (`@theme`)

Design tokens define Idno's visual vocabulary as CSS custom properties. This is where future themes do most of their customization — override token values and everything downstream responds.

**`src/css/tokens.css`:**

```css
@theme {
  /* Colors */
  --color-bg: #f3f4f6;
  --color-surface: #ffffff;
  --color-border: #e5e7eb;
  --color-border-subtle: #f0f0f0;
  --color-text: #374151;
  --color-text-strong: #111111;
  --color-text-muted: #9ca3af;
  --color-text-secondary: #6b7280;
  --color-primary: #111111;
  --color-primary-text: #ffffff;

  /* Spacing */
  --spacing-card: 1.5rem;      /* 24px — internal card padding */
  --spacing-gap: 0.75rem;      /* 12px — gap between elements */
  --spacing-section: 1rem;     /* 16px — between sections */

  /* Border radius */
  --radius-sm: 0.5rem;         /* 8px */
  --radius-md: 0.75rem;        /* 12px */
  --radius-lg: 1rem;           /* 16px */
  --radius-full: 9999px;

  /* Typography */
  --font-sans: -apple-system, system-ui, sans-serif;
  --font-size-sm: 0.8125rem;   /* 13px */
  --font-size-base: 0.9375rem; /* 15px */
  --font-size-lg: 1.125rem;    /* 18px */
  --font-size-title: 1.75rem;  /* 28px */

  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
  --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
  --shadow-modal: 0 20px 60px rgba(0, 0, 0, 0.15);

  /* Admin */
  --color-admin-bg: #111111;
  --color-admin-text: rgba(255, 255, 255, 0.5);
  --color-admin-text-active: rgba(255, 255, 255, 0.9);
  --color-admin-divider: rgba(255, 255, 255, 0.08);
}
```

#### Layer 2: Component Classes (`@layer components`)

Semantic classes that plugin authors use in templates. Written in plain CSS referencing token variables. This is the **stable API** — documented as the contract between core, themes, and plugins. All component classes use the `idno-` prefix to avoid collisions.

**`src/css/components/post-card.css`:**

```css
@layer components {
  .idno-entry {
    background: var(--color-surface);
    border-radius: var(--radius-md);
    border: 1px solid var(--color-border-subtle);
    padding: var(--spacing-card);
    margin-bottom: var(--spacing-gap);
    box-shadow: var(--shadow-sm);
  }

  .idno-entry-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-gap);
    margin-bottom: var(--spacing-gap);
  }

  .idno-entry-avatar {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: var(--radius-full);
    object-fit: cover;
  }

  .idno-entry-author {
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--color-text-strong);
  }

  .idno-entry-meta {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
  }

  .idno-entry-body {
    font-size: var(--font-size-base);
    color: var(--color-text);
    line-height: 1.6;
  }
}
```

**`src/css/components/buttons.css`:**

```css
@layer components {
  .idno-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    font-size: var(--font-size-sm);
    font-weight: 600;
    cursor: pointer;
    border: none;
  }

  .idno-btn-primary {
    background: var(--color-primary);
    color: var(--color-primary-text);
  }

  .idno-btn-ghost {
    background: transparent;
    border: 1px solid var(--color-border);
    color: var(--color-text);
  }
}
```

#### Layer 3: Tailwind Utilities

Tailwind's generated utility classes sit on top for one-off adjustments in templates. Because Tailwind v4 uses native CSS cascade layers, utilities automatically win over component classes in specificity — a plugin can use `.idno-entry` for the base and add `mt-4` or `text-sm` for local tweaks without specificity conflicts.

#### Entry point — `src/css/main.css`

```css
@import "tailwindcss";
@import "./tokens.css";
@import "./components/layout.css";
@import "./components/navigation.css";
@import "./components/post-card.css";
@import "./components/forms.css";
@import "./components/buttons.css";
@import "./components/modal.css";
@import "./components/editor.css";
@import "./components/profile.css";
@import "./components/admin.css";
```

#### Theming via token overrides

Future themes that want to customize the Idno 2026 look can override just the token values. For example, a dark mode variant only needs to change the token layer:

```css
@theme {
  --color-bg: #0f0f0f;
  --color-surface: #1a1a1a;
  --color-border: #2a2a2a;
  --color-text: #e5e5e5;
  --color-text-strong: #ffffff;
  /* ... */
}
```

All component classes automatically respond — no CSS rewriting needed.

#### Plugin developer experience

Plugin authors use the `idno-` prefixed component classes in their templates. They don't need to know Tailwind. The classes are documented and stable:

```html
<div class="idno-entry h-entry">
  <div class="idno-entry-header p-author h-card">
    <img class="idno-entry-avatar u-photo" src="..." />
    <span class="idno-entry-author p-name">Name</span>
  </div>
  <div class="idno-entry-body e-content">Content</div>
</div>
```

For one-off adjustments, Tailwind utilities can be mixed in: `class="idno-entry mt-4"` — utilities always win due to cascade layers.

## JavaScript Architecture

### Forked JS with Alpine.js

A new JS bundle replaces the current `idno.min.js` (which depends on jQuery + Bootstrap JS).

**Entry point** — `src/js/main.js`:

```js
import Alpine from 'alpinejs';
import './modules/mentions.js';
import './modules/interactions.js';
import './modules/media.js';
import './modules/editor.js';
import './modules/compose.js';
import './modules/autosave.js';

window.Alpine = Alpine;
Alpine.start();
```

- **Alpine.js** handles interactive UI: dropdowns, modals, toggles, tabs — via declarative attributes in templates (`x-show`, `x-on:click`, `x-data`)
- **Tiptap** replaces TinyMCE for rich text editing (see Editor section)
- **No jQuery, no Bootstrap JS** — old themes continue loading them via the unmodified `idno.min.js`

### Modules carried over from current JS

The following logic from `js/src/` is ported to the new bundle, rewritten to remove jQuery/Bootstrap dependencies:

- Mention autocomplete (`@` mentions)
- Interaction handlers (like, reply, repost API calls)
- Media upload and file handling
- Autosave (periodic form persistence)

## Visual Design

### Aesthetic: Clean Minimal

- White cards on light gray background (`#f3f4f6`)
- Subtle borders and minimal shadows (token-driven via `--color-border-subtle` and `--shadow-sm`)
- Generous whitespace
- System font stack: `-apple-system, system-ui, sans-serif`
- Content-focused with minimal chrome
- Modern, polished — comparable quality bar to contemporary social platforms

### Icons: Lucide

- Lucide icon library (ISC license)
- SVG-based, tree-shakeable
- ISC license file included at `Themes/2026/LICENSE-lucide.txt`
- 1000+ icons available

## Layout

### Public site: Left navigation + centered feed

**Desktop:**
- Left sidebar with icons + labels, flat on the background (no card/container)
- Logo/wordmark at top
- Nav items: Home, Profile, Search, Settings
- Note: Notifications is not included — Idno does not currently have a notifications system. This can be added in a future iteration.
- "New Post" button at bottom of nav
- Active item highlighted with subtle background
- Main feed area centered, max-width constrained

**Mobile:**
- Left nav collapses to bottom tab bar with icons only
- Feed takes full width

### Admin: Dark sidebar

- Dark (`#111`) left sidebar, clearly distinguishing admin from public site
- Icons + labels for all admin sections
- Sections: Dashboard, Themes, Plugins, Users, Email (divider) Statistics, Logs, Import/Export
- Note: "Site Settings" maps to the existing `/admin/` home page (`home.tpl.php`), not a separate settings page
- "Back to site" link at bottom of sidebar
- Main content area on light background with cards for settings groups
- Dashboard shows stats cards (posts, users, webmentions) and recent activity feed

## New Post Flow

### Content type picker

1. User clicks "New Post" button in left nav
2. Modal opens with a 2-column grid of content types
3. Each type shows: Lucide icon (via `getIconName()`), name (via `getTitle()`), and short description (via `getDescription()`)
4. Content types are **fully dynamic** — populated from installed plugins via the `ContentType` registry. Any plugin that registers a `ContentType` subclass automatically appears in the picker. The theme does not hardcode any content types.
5. Selecting a type transitions the modal to the compose form for that type
6. If a plugin provides a content type but no `templates/modern/` edit template, the template resolver falls back to `templates/default/` — the Bootstrap-styled form renders within the modal's content area. It won't look perfectly styled but remains functional. This is the natural template type fallback mechanism.

### Compose form (general pattern)

- Modal with header bar: back arrow (returns to type picker), content type name, draft badge, autosave indicator, Publish button
- Author avatar + name at top (for status-type posts)
- Content area specific to the content type
- Below content, inline settings (no hidden panel):
  - **Tags** — inline tag chips with add/remove
  - **Visibility** — toggle buttons: Public / Members / Private
  - **In Reply To** — URL input for webmention reply targets
  - **Syndicate To** — checkboxes for configured POSSE targets
- Bottom bar: Save Draft + Publish buttons

### Article editor (Tiptap)

- Inline title field (large, bold, no input chrome — placeholder "Give it a title")
- Inline subtitle field (smaller, gray — placeholder "Optional subtitle")
- Tiptap toolbar in a rounded container:
  - Paragraph style dropdown (Paragraph, Heading 1-3)
  - Bold, Italic, Strikethrough, Inline code
  - Link, Image
  - Blockquote, Bullet list, Numbered list
  - Code block, Horizontal rule
- Rich text editing area below toolbar
- All settings inline below editor (tags, visibility, in-reply-to, syndicate-to)
- Autosave every 10 seconds, using localStorage (matching current `Template.autoSave()` behavior from `js/src/`). The existing autosave logic persists form field values by element ID and restores on page load. The new module ports this pattern without jQuery.

## New Features Required

### Draft UI (exposing existing system)

The core codebase already has a `publish_status` field on entities with `setPublishStatus()` and `getPublishStatus()` methods supporting `'published'`, `'draft'`, and `'scheduled'` values. However, the current UI does not fully expose this. This design surfaces the existing draft system in the new compose flow:

- The compose form shows current status as a badge ("Draft" / "Published")
- "Save Draft" button sets `publish_status` to `'draft'` and saves without triggering webmentions/syndication
- "Publish" button sets `publish_status` to `'published'` and triggers webmentions/syndication
- Drafts are accessible via `/drafts/` — a new page listing the current user's draft entries, linked from the left nav (visible only to the logged-in author)
- The drafts page template is added to the theme: `templates/modern/drafts.tpl.php`

### ContentType API additions

Two new methods on the `ContentType` base class (`Idno/Common/ContentType.php`):

**`getDescription()`** — short human-readable description for the content type picker:

- Returns a string (e.g., "Short update", "Long-form post")
- Default implementation returns an empty string (picker shows name only for third-party types that don't override this)
- Populated for all bundled plugins:
  - **Status**: "Short update"
  - **Text/Article**: "Long-form post"
  - **Photo**: "Image post"
  - **Event**: "Date & location"
  - **Checkin**: "Share location"
  - **Like/Bookmark**: "Save a link"

**`getIconName()`** — Lucide icon name for the content type picker:

- Returns a Lucide icon name as a string (e.g., `'message-square'`, `'image'`, `'calendar'`)
- Default implementation returns `'file-text'` — a sensible generic for any content type
- This is separate from the existing `getIcon()` method, which returns rendered HTML from a template. `getIconName()` returns just the icon identifier string so the theme can render it however it wants.
- Populated for all bundled plugins:
  - **Status**: `'message-square'`
  - **Text/Article**: `'newspaper'`
  - **Photo**: `'image'`
  - **Event**: `'calendar'`
  - **Checkin**: `'map-pin'`
  - **Like/Bookmark**: `'bookmark'`

Third-party plugins that don't implement these methods get the defaults (no description, generic icon) and still appear in the picker.

### Plugin `getAdminIcon()` method

Add a `getAdminIcon()` method to the plugin base class (`Idno/Common/Plugin.php`):

- Returns a Lucide icon name as a string (e.g., `'message-square'`, `'image'`, `'calendar'`)
- Default implementation returns `'box'` — a generic "plugin" icon
- The admin nav template renders the icon by name from the Lucide set
- Populated for all bundled plugins with appropriate icons

### Admin menu plugin extensibility

Currently, plugins inject admin menu items by extending the `admin/menu/items` template with raw `<li>` HTML. The new admin nav template must preserve this extension point:

- The new `admin/menu.tpl.php` continues to call `$this->draw('admin/menu/items')` at the appropriate position in the sidebar
- Plugin-injected menu items that use the old HTML format (Bootstrap `<li>` tags with Fork Awesome icons) will render inside the dark sidebar but won't match the Tailwind styling. This is acceptable — the items remain functional.
- Plugins that want their admin menu items to look native in the Idno 2026 theme should:
  1. Override `getAdminIcon()` to return a Lucide icon name
  2. Provide a theme-specific `admin/menu/items` extension template that uses `idno-` classes
- The admin nav template also iterates over registered plugins and renders icons from `getAdminIcon()` for the core navigation items. Plugin-added items via `admin/menu/items` appear in a separate "Plugins" section of the sidebar.

## Microformats — Hard Constraint

All templates MUST preserve full microformats2 markup. Styling classes are cosmetic; microformat classes are semantic. Both are always present.

### Required microformat classes

**Root objects:**
- `h-entry` on all content items
- `h-card` on author/profile information
- `h-cite` on comments/annotations
- `h-feed` on feed containers

**Properties (always present on appropriate elements):**
- `p-name` — title/name
- `e-content` — full body content
- `p-summary` — summary/excerpt
- `p-author` — author container
- `u-url` — permalink
- `u-photo` — profile/content photos
- `dt-published` — publication datetime (with `datetime` attribute)
- `u-syndication` — POSSE links (with `rel="syndication"`)
- `u-in-reply-to` — reply target links
- `u-like-of`, `u-repost-of`, `u-bookmark-of` — interaction targets

**Rel attributes:**
- `rel="me"` — identity links on profiles
- `rel="webmention"` — webmention endpoint
- `rel="micropub"` — micropub endpoint
- `rel="authorization_endpoint"` — IndieAuth
- `rel="token_endpoint"` — token endpoint
- `rel="permalink"` — permanent link

### Template example

```html
<article class="idno-entry h-entry">
  <div class="idno-entry-header p-author h-card">
    <a href="..." class="u-url">
      <img class="idno-entry-avatar u-photo" src="..." alt="..." />
    </a>
    <a href="..." class="idno-entry-author p-name u-url">Author Name</a>
  </div>
  <div class="idno-entry-body e-content">
    Content here
  </div>
  <footer class="idno-entry-footer">
    <a class="idno-entry-permalink u-url" href="..." rel="permalink">
      <time class="dt-published" datetime="2026-03-11T10:00:00+00:00">
        March 11, 2026
      </time>
    </a>
  </footer>
</article>
```

## Plugin Extensibility

Idno's architecture is plugin-driven. Content types, admin pages, and UI elements are all added by plugins. The Idno 2026 theme must handle plugins gracefully — both bundled ones and unknown third-party ones.

### Content types are plugins

Every content type (Status, Article, Photo, Event, Checkin, Like) is a plugin that registers a `ContentType` subclass. The new post modal, feed rendering, and entity display **never hardcode content types**. They always iterate over whatever `ContentType` instances are registered.

A third-party plugin that adds a new content type (e.g., a Podcast plugin) will:
- Appear in the content type picker automatically (with default icon and no description unless it overrides `getIconName()` and `getDescription()`)
- Have its edit template rendered in the compose modal — if the plugin provides `templates/modern/` templates, those are used; otherwise the resolver falls back to `templates/default/` (Bootstrap-styled, functional but visually inconsistent)
- Have its display template rendered in the feed (same graceful degradation)
- **Migration path for third-party plugins**: Add a `templates/modern/` directory with Tailwind-styled templates using `idno-` component classes. No other changes needed — the template type system handles the rest.

### Admin navigation is plugin-extensible

Plugins add admin menu items by extending the `admin/menu/items` template. The new admin nav must:
- Continue calling `$this->draw('admin/menu/items')` so plugin-added items appear
- Render plugin-added items in a distinct section of the sidebar (after the core items, with a divider)
- Fall back to the `'box'` icon for plugins that don't implement `getAdminIcon()`
- Accept that plugin-injected HTML may not match the dark sidebar styling — functional but visually inconsistent is acceptable; plugins can provide theme-specific overrides

### Shell templates and plugin assets

Plugins register their own CSS and JS via `Page::getAssets()`. The new shell templates must continue to render plugin-registered assets:
- `$this->draw('shell/head')` extensions from plugins (meta tags, additional CSS)
- Plugin CSS via `site()->currentPage()->getAssets('css')`
- Plugin JS via `site()->currentPage()->getAssets('js')`

These are loaded alongside the theme's own assets, so plugin styles and scripts continue to work.

### Template type as the plugin migration path

Plugin developers who want their templates to look native in the Idno 2026 theme add a `templates/modern/` directory to their plugin. The template type resolution handles the rest:

1. When `modern` is the active template type, the resolver looks for `templates/modern/{templateName}.tpl.php` first
2. If found (in any registered path — theme, plugin, or core), it's used
3. If not found, falls back to `templates/default/{templateName}.tpl.php`

This means plugins can migrate one template at a time. A plugin might provide a `modern` version of its edit form but let the display template fall back to `default`. No all-or-nothing migration required.

## Backwards Compatibility

### No shim, no emulation

- Old themes continue to use the `default` template type and load Bootstrap 3 CSS/JS via the unmodified core templates
- Old plugins that only provide `templates/default/` continue to work — their `default` templates are used as fallback when the `modern` type doesn't have a match
- When a `default` template renders inside the `modern` shell (Tailwind context), Bootstrap-specific classes won't be styled, but native HTML form elements, links, and buttons remain functional
- The Idno 2026 theme only affects rendering when the `modern` template type is active

### Migration path to core default

When ready to promote the `modern` template type to be the primary:

1. Move `modern` plugin templates into the core template directory structure
2. Move build config and `src/` to project root
3. Update asset paths in shell templates
4. Make `modern` the default template type (or rename it to `default` and rename the current `default` to `legacy`)
5. The `modern` template type and per-plugin migration pattern is designed to make this transition incremental

## Dependencies (new)

| Dependency | Version | License | Purpose |
|-----------|---------|---------|---------|
| Tailwind CSS | 4.x | MIT | Utility CSS framework (v4 for native `@theme`, `@layer`, and cascade layer support) |
| Alpine.js | 3.x | MIT | Lightweight reactive JS |
| Tiptap | 2.x | MIT | Headless rich text editor (ProseMirror) |
| Lucide | latest | ISC | Icon library |
| Vite | 5.x+ | MIT | Build tool |

All dependencies are MIT or ISC licensed.
