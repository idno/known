# Idno 2026 Theme — Design Specification

## Overview

Replace Idno's Bootstrap 3 frontend with a modern, polished UI built on Tailwind CSS. The new look ships as a theme called "Idno 2026" under `Themes/2026/`, using the existing theme override system. The admin panel also gets the Tailwind treatment. Existing themes and plugin templates continue to load Bootstrap as they always have — no shim, no emulation.

This theme is designed as the future default. When the time comes, promoting it to core means moving templates from `Themes/2026/` into `templates/default/`.

## Architecture

### Approach: Theme-Only (minimal core changes)

Everything lives under `Themes/2026/`. The theme uses the existing template override mechanisms — `theme.ini` extensions, replacements, and prepends — to swap out shell templates, entity templates, and admin templates. No changes to core rendering code.

Three small additions to core PHP classes support the theme but are not theme-specific — they extend the plugin API for all themes:
- `ContentType::getDescription()` — short description for content type pickers
- Plugin base class `getAdminIcon()` — Lucide icon name for admin navigation
- UI exposure of the existing `publish_status` draft system

- **New theme + admin**: Load Tailwind CSS, Alpine.js, Tiptap. No Bootstrap, no jQuery.
- **Old themes/plugins**: Load Bootstrap CSS/JS as they always have. No Tailwind.

The template system handles this naturally: the shell templates control what CSS/JS gets loaded. The new theme replaces `shell/bootstrap.tpl.php` with an empty file (preventing Bootstrap from loading) and provides its own shell that loads the modern stack.

### Theme directory structure

```
Themes/2026/
├── theme.ini                      # Template replacements and extensions
├── Controller.php                 # Theme initialization
├── preview.png                    # Theme selector preview image
├── package.json                   # Node dependencies
├── vite.config.js                 # Vite build configuration
├── tailwind.config.js             # Tailwind configuration
├── src/
│   ├── css/
│   │   ├── main.css               # Tailwind directives + component imports
│   │   └── components/            # Component class definitions
│   │       ├── layout.css         # Page shell, grid, spacing
│   │       ├── navigation.css     # Left nav, mobile bottom bar
│   │       ├── post-card.css      # Feed items, entry display
│   │       ├── forms.css          # Inputs, textareas, selects
│   │       ├── buttons.css        # Button variants
│   │       ├── modal.css          # Compose modal, dialogs
│   │       ├── editor.css         # Tiptap editor styling
│   │       ├── profile.css        # User profile pages
│   │       ├── admin.css          # Admin panel styles
│   │       └── utilities.css      # One-off helpers
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
│   └── default/
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

## Build System

### Vite (new theme)

Vite handles the new theme's assets inside `Themes/2026/`:

- **Input**: `src/css/main.css` and `src/js/main.js`
- **Output**: `dist/modern.min.css` and `dist/modern.min.js`
- **Tailwind** scans `Themes/2026/templates/` for class usage, purging unused utilities
- **Development**: `cd Themes/2026 && npm run dev` (Vite watch mode)
- **Production**: `npm run build` outputs to `dist/`, which is committed to the repo so the theme works without Node.js in production. The project `.gitignore` must not exclude `Themes/2026/dist/`.

### Grunt (legacy — untouched)

Grunt continues to build `css/idno.css` and `js/idno.min.js` for Bootstrap-based themes. No changes to `Gruntfile.js`. The two build systems are fully independent.

## CSS Architecture

### Component classes with `@apply`

Tailwind utility classes are composed into semantic component classes using `@apply`. This keeps templates readable and makes the system accessible to plugin/theme developers who don't need deep Tailwind knowledge.

**Entry point** — `src/css/main.css`:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@import './components/layout.css';
@import './components/navigation.css';
@import './components/post-card.css';
/* ... etc */
```

**Component file example** — `src/css/components/post-card.css`:

```css
.post-card {
  @apply bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4;
}
.post-card-header {
  @apply flex items-center gap-3 mb-3;
}
.post-card-avatar {
  @apply w-10 h-10 rounded-full object-cover;
}
.post-card-meta {
  @apply text-sm text-gray-500;
}
.post-card-body {
  @apply prose prose-sm max-w-none;
}
```

**Hybrid usage**: Tailwind utilities may be used directly in templates for one-off layout concerns (e.g., a specific `mt-4` or `flex`), but reusable visual patterns are always defined as component classes.

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
- Subtle borders (`border-gray-100`) and minimal shadows (`shadow-sm`)
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
3. Each type shows: colored icon, name, and short description
4. Content types are dynamic — populated from installed plugins via the `ContentType` registry
5. Selecting a type transitions the modal to the compose form for that type

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
- The drafts page template is added to the theme: `templates/default/drafts.tpl.php`

### ContentType `getDescription()` method

Add a `getDescription()` method to the `ContentType` base class:

- Returns a short string describing the content type (e.g., "Short update", "Long-form post")
- Default implementation returns an empty string
- The new post modal displays the description below the content type name
- Populated for all bundled plugins:
  - **Status**: "Short update"
  - **Text/Article**: "Long-form post"
  - **Photo**: "Image post"
  - **Event**: "Date & location"
  - **Checkin**: "Share location"
  - **Like/Bookmark**: "Save a link"

### Plugin `getAdminIcon()` method

Add a `getAdminIcon()` method to the plugin base class:

- Returns a Lucide icon name as a string (e.g., `'message-square'`, `'image'`, `'calendar'`)
- Default implementation returns a generic icon (e.g., `'box'`) for plugins that don't define one
- The admin nav template renders the icon by name from the Lucide set
- Populated for all bundled plugins with appropriate icons

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
<article class="post-card h-entry">
  <div class="post-card-header p-author h-card">
    <a href="..." class="u-url">
      <img class="post-card-avatar u-photo" src="..." alt="..." />
    </a>
    <a href="..." class="post-card-author p-name u-url">Author Name</a>
  </div>
  <div class="post-card-body e-content">
    Content here
  </div>
  <footer class="post-card-footer">
    <a class="post-card-permalink u-url" href="..." rel="permalink">
      <time class="dt-published" datetime="2026-03-11T10:00:00+00:00">
        March 11, 2026
      </time>
    </a>
  </footer>
</article>
```

## Backwards Compatibility

### No shim, no emulation

- Old themes continue to load Bootstrap 3 CSS/JS via the unmodified core templates
- Old plugins that register their own templates continue to work — their templates render with Bootstrap as before
- The Idno 2026 theme only affects templates it explicitly replaces
- Plugin templates that aren't overridden by the theme will render with whatever CSS the current shell loads — plugin developers who want their templates to look right in both Bootstrap and Tailwind contexts should provide theme-specific template overrides

### Migration path to core default

When ready to promote Idno 2026 to the default:

1. Move templates from `Themes/2026/templates/` to `templates/default/`
2. Move build config and `src/` to project root
3. Update asset paths in shell templates
4. Bootstrap-based themes become the ones that need to override the shell to load Bootstrap instead
5. The current `Themes/2026/` structure is designed to make this migration straightforward

## Dependencies (new)

| Dependency | Version | License | Purpose |
|-----------|---------|---------|---------|
| Tailwind CSS | 3.x | MIT | Utility CSS framework (v3 chosen for stable `@apply` support; v4 changed config model and has known `@apply` issues) |
| Alpine.js | 3.x | MIT | Lightweight reactive JS |
| Tiptap | 2.x | MIT | Headless rich text editor (ProseMirror) |
| Lucide | latest | ISC | Icon library |
| Vite | 5.x | MIT | Build tool |

All dependencies are MIT or ISC licensed.
