# Admin & Account Settings Shell Redesign — Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the shared `settings-shell.tpl.php` with two self-contained shells (`admin-shell.tpl.php` and `account-shell.tpl.php`) that are isolated from theme `shell/*` subtemplates, fixing double-rendering bugs, unstyled account settings, and theme bleed-through.

**Architecture:** Each shell is a complete HTML document that loads CSS/JS directly via hardcoded `<link>`/`<script>` tags to Twenty26's built assets. Admin page templates stop calling `draw('admin/shell')` — the page shell itself provides the sidebar. Account settings gets a new light-sidebar menu template.

**Tech Stack:** PHP templates (Known/Idno template system), CSS (Tailwind v4 via PostCSS + Vite), Lucide SVG icons via `shell/icon` helper.

**Spec:** `docs/superpowers/specs/2026-03-12-admin-account-settings-redesign.md`

**Visual mockups:** `.superpowers/brainstorm/75779-1773347656/full-mockup-v2.html`

---

## Chunk 1: Core PHP + Admin Shell

### Task 1: Register dedicated shell overrides in core PHP

**Files:**
- Modify: `Idno/Core/Admin.php` (line ~40)
- Modify: `Idno/Core/Account.php` (line ~41)

These two one-line changes route `/admin/` URLs to `admin-shell` and `/account/` URLs to `account-shell` instead of the shared `settings-shell`.

- [ ] **Step 1: Update Admin.php shell override**

In `Idno/Core/Admin.php`, change:
```php
\Idno\Core\Idno::site()->template()->addUrlShellOverride('admin', 'settings-shell');
```
to:
```php
\Idno\Core\Idno::site()->template()->addUrlShellOverride('admin', 'admin-shell');
```

- [ ] **Step 2: Update Account.php shell override**

In `Idno/Core/Account.php`, change:
```php
Idno::site()->template()->addUrlShellOverride('account', 'settings-shell');
```
to:
```php
Idno::site()->template()->addUrlShellOverride('account', 'account-shell');
```

- [ ] **Step 3: Commit**

```bash
git add Idno/Core/Admin.php Idno/Core/Account.php
git commit -m "feat: route admin and account to dedicated shell templates

Change addUrlShellOverride calls to use 'admin-shell' and
'account-shell' instead of the shared 'settings-shell'."
```

> **Note:** The site will break after this commit until Tasks 2-3 are complete (the new shell templates don't exist yet). This is expected — we're building the foundation first.

---

### Task 2: Create the self-contained admin-shell.tpl.php

**Files:**
- Create: `Themes/Twenty26/templates/modern/admin-shell.tpl.php`

This is the complete page shell for all `/admin/` URLs. It replaces `settings-shell.tpl.php` for admin pages. It's self-contained: own `<html>`, `<head>`, hardcoded CSS/JS loading, sidebar with admin menu, content area. Does NOT draw from `shell/*` subtemplates (except `shell/messages` for flash messages, `shell/form-data` for CSRF, and `shell/icon` for icons which are theme-internal utilities, not shell chrome).

- [ ] **Step 1: Create admin-shell.tpl.php**

Create `Themes/Twenty26/templates/modern/admin-shell.tpl.php`:

```php
<?php
    $template = $this->formatShellVariables($vars);
    $vars = $template->vars;
    $template->draw('shell/headers');
?>
<!DOCTYPE html>
<html lang="<?= $vars['lang'] ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(\Idno\Core\Idno::site()->config()->getTitle()) ?> — Admin</title>
    <link rel="stylesheet" href="<?= \Idno\Core\Idno::site()->config()->getStaticURL() ?>Themes/Twenty26/dist/modern.min.css">
<?php
    // Render any plugin-registered CSS assets
    if ($assets = \Idno\Core\Idno::site()->currentPage()->getAssets('css')) {
        foreach ($assets as $asset) {
            echo '    <link rel="stylesheet" href="' . htmlspecialchars($asset) . '">' . "\n";
        }
    }
?>
</head>
<body class="admin-template">
    <div class="idno-admin-shell">
        <aside class="idno-admin-sidebar">
            <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/" class="idno-admin-logo">
                <?= htmlspecialchars(\Idno\Core\Idno::site()->config()->getTitle()) ?>
            </a>
            <?= $this->draw('admin/menu') ?>
            <div style="margin-top:auto;padding:0 0.75rem">
                <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>" class="idno-admin-nav-item">
                    <?= $this->__(['icon' => 'arrow-left'])->draw('shell/icon') ?>
                    <span><?= \Idno\Core\Idno::site()->language()->_('Back to site') ?></span>
                </a>
            </div>
        </aside>
        <main class="idno-admin-main">
            <div class="idno-admin-content">
                <?= $template->draw('shell/messages') ?>
                <?php
                    if (!empty($vars['body'])) {
                        echo $vars['body'];
                    }
                ?>
            </div>
        </main>
    </div>
    <script src="<?= \Idno\Core\Idno::site()->config()->getStaticURL() ?>Themes/Twenty26/dist/modern.min.js" defer></script>
<?php
    // Render any plugin-registered JS assets
    if ($assets = \Idno\Core\Idno::site()->currentPage()->getAssets('javascript')) {
        foreach ($assets as $asset) {
            echo '    <script src="' . htmlspecialchars($asset) . '"></script>' . "\n";
        }
    }
    echo $template->draw('shell/form-data');
?>
</body>
</html>
```

**Key differences from the old `settings-shell.tpl.php`:**
- No `shell/nav` — admin has its own sidebar navigation
- No `shell/opengraph`, `shell/structured-data`, `shell/dublincore`, `shell/amp`, `shell/syndication`, `shell/activitypub`, `shell/monetization` — not needed for admin pages
- No `shell/bootstrap` — Twenty26 doesn't use Bootstrap
- CSS and JS loaded directly via hardcoded paths, not via `shell/css` and `shell/footerjavascript`
- Plugin CSS/JS assets still rendered (plugins may register admin-specific stylesheets)
- `shell/messages` kept for flash messages
- `shell/form-data` kept for CSRF tokens and dynamic form content
- `shell/headers` kept for HTTP headers
- The sidebar is part of the shell itself (no separate `admin/shell` body wrapper)

- [ ] **Step 2: Verify admin page loads in browser**

Navigate to `https://idno:8890/admin/` — you should see the admin dashboard with the dark sidebar and no double-rendering. The page title and content should appear in the main content area.

> **Note:** The admin pages will still call `draw('admin/shell')` at this point, which means the sidebar will appear twice — once from `admin-shell.tpl.php` and once from `admin/shell.tpl.php`. This is expected and will be fixed in Task 3.

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/admin-shell.tpl.php
git commit -m "feat: add self-contained admin-shell.tpl.php

Complete HTML page shell for admin pages. Loads CSS/JS directly
without drawing from shell/* subtemplates. Includes sidebar,
admin menu, messages, and form-data support."
```

---

### Task 3: Update all admin page templates to stop drawing admin/shell

**Files:**
- Modify: `Themes/Twenty26/templates/modern/admin/home.tpl.php`
- Modify: `Themes/Twenty26/templates/modern/admin/plugins.tpl.php`
- Modify: `Themes/Twenty26/templates/modern/admin/themes.tpl.php`
- Modify: `Themes/Twenty26/templates/modern/admin/users.tpl.php`
- Modify: `Themes/Twenty26/templates/modern/admin/email.tpl.php`
- Modify: `Themes/Twenty26/templates/modern/admin/statistics.tpl.php`
- Modify: `Themes/Twenty26/templates/modern/admin/import.tpl.php`
- Modify: `Themes/Twenty26/templates/modern/admin/export.tpl.php`
- Modify: `Themes/Twenty26/templates/modern/admin/logs.tpl.php`

Every admin page template currently follows this pattern:
```php
<?php ob_start(); ?>
    <!-- page content -->
<?php
    $content = ob_get_clean();
    echo $this->__([
        'body' => $content,
        'title' => 'Page Title'
    ])->draw('admin/shell');
?>
```

The `draw('admin/shell')` wraps content in a second sidebar layout (the body wrapper), which causes double-rendering now that `admin-shell.tpl.php` already provides the sidebar. Each template must be changed to output content directly, with the page title as an `<h1>`.

The new pattern is:
```php
<h1 class="idno-admin-page-title">Page Title</h1>
<!-- page content (no ob_start/ob_get_clean/draw wrapper) -->
```

- [ ] **Step 1: Update admin/home.tpl.php**

Three changes to make:

1. **Delete line 2** (`<?php ob_start(); ?>`)
2. **Insert at the top** (where `ob_start()` was): `<h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('Site Settings') ?></h1>`
3. **Delete lines 148-154** (the `ob_get_clean()`/`draw('admin/shell')` block):
```php
<?php
    $content = ob_get_clean();
    echo $this->__([
        'body' => $content,
        'title' => \Idno\Core\Idno::site()->language()->_('Site Settings')
    ])->draw('admin/shell');
?>
```

All content between (the form, cards, etc.) stays identical.

- [ ] **Step 2: Update admin/plugins.tpl.php**

Same pattern — remove `ob_start()`/`ob_get_clean()`/`draw('admin/shell')` wrapper, add `<h1>`:

```php
<h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('Plugins') ?></h1>

<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('Plugins allow you to add features to your site. These include new kinds of content, options to syndicate content to different sites, and features to change the way Idno behaves. To enable or disable a plugin, just click its enable or disable button.') ?>
</p>

<?php
$display = [];
if (!empty($vars['plugins_stored']) && is_array($vars['plugins_stored'])) {
    foreach ($vars['plugins_stored'] as $shortname => $plugin) {
        if (\Idno\Core\Idno::site()->plugins()->isVisible($shortname)) {
            $plugin['shortname'] = $shortname;
            $display[$plugin['Plugin description']['name']] = $this->__(array('plugin' => $plugin))->draw('admin/plugins/plugin');
        }
    }
}
ksort($display);
echo implode('', $display);
?>
```

- [ ] **Step 3: Update admin/themes.tpl.php**

Same pattern:

```php
<h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('Themes') ?></h1>

<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('Themes allow you to change the way your site looks. The following themes are installed.') ?>
</p>

<?php
if (!empty($vars['themes_stored']) && is_array($vars['themes_stored'])) {
    $currentTheme = !empty($vars['theme']) ? $vars['theme'] : false;

    // Draw active theme first
    foreach ($vars['themes_stored'] as $shortname => $theme) {
        $theme['shortname'] = $shortname;
        if ($theme['shortname'] == $currentTheme) {
            echo $this->__(array('theme' => $theme))->draw('admin/themes/theme');
        }
    }

    // Draw remaining themes
    foreach ($vars['themes_stored'] as $shortname => $theme) {
        $theme['shortname'] = $shortname;
        if ($theme['shortname'] != $currentTheme) {
            echo $this->__(array('theme' => $theme))->draw('admin/themes/theme');
        }
    }
}
?>
```

- [ ] **Step 4: Update admin/users.tpl.php**

Same three-step edit. Title: `\Idno\Core\Idno::site()->language()->_('Users')`

- [ ] **Step 5: Update admin/email.tpl.php**

Same three-step edit. Title: `\Idno\Core\Idno::site()->language()->_('Email')`

- [ ] **Step 6: Update admin/statistics.tpl.php**

Same three-step edit. Title: `\Idno\Core\Idno::site()->language()->_('Statistics')`

- [ ] **Step 7: Update admin/import.tpl.php**

Same three-step edit. Title: `\Idno\Core\Idno::site()->language()->_('Import')`

- [ ] **Step 8: Update admin/export.tpl.php**

Same three-step edit. Title: `\Idno\Core\Idno::site()->language()->_('Export')`

- [ ] **Step 9: Update admin/logs.tpl.php**

Same three-step edit. Title: `\Idno\Core\Idno::site()->language()->_('Logs')`

- [ ] **Step 10: Verify all admin pages render correctly**

Navigate to each admin page in the browser and verify:
- Single sidebar (no ghost duplicates)
- Page title shows in content area
- Forms and cards render correctly
- Plugin menu items appear in sidebar

Pages to check:
- `https://idno:8890/admin/` (Dashboard / Site Settings)
- `https://idno:8890/admin/plugins/`
- `https://idno:8890/admin/themes/`
- `https://idno:8890/admin/users/`
- `https://idno:8890/admin/email/`
- `https://idno:8890/admin/statistics/`
- `https://idno:8890/admin/import/`
- `https://idno:8890/admin/export/`

- [ ] **Step 11: Commit**

```bash
git add Themes/Twenty26/templates/modern/admin/home.tpl.php \
        Themes/Twenty26/templates/modern/admin/plugins.tpl.php \
        Themes/Twenty26/templates/modern/admin/themes.tpl.php \
        Themes/Twenty26/templates/modern/admin/users.tpl.php \
        Themes/Twenty26/templates/modern/admin/email.tpl.php \
        Themes/Twenty26/templates/modern/admin/statistics.tpl.php \
        Themes/Twenty26/templates/modern/admin/import.tpl.php \
        Themes/Twenty26/templates/modern/admin/export.tpl.php \
        Themes/Twenty26/templates/modern/admin/logs.tpl.php
git commit -m "refactor: remove draw('admin/shell') wrapper from all admin templates

Admin page templates now output content directly instead of wrapping
in admin/shell body wrapper. The admin-shell.tpl.php page shell
provides the sidebar, so the wrapper caused double-rendering."
```

---

### Task 4: Delete the old admin/shell.tpl.php body wrapper

**Files:**
- Delete: `Themes/Twenty26/templates/modern/admin/shell.tpl.php`

Now that no admin page template calls `draw('admin/shell')`, the body wrapper is dead code.

- [ ] **Step 1: Delete admin/shell.tpl.php**

```bash
rm Themes/Twenty26/templates/modern/admin/shell.tpl.php
```

- [ ] **Step 2: Verify admin pages still work**

Navigate to `https://idno:8890/admin/` — should render correctly with single sidebar.

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/admin/shell.tpl.php
git commit -m "cleanup: delete admin/shell.tpl.php body wrapper

No longer needed — admin-shell.tpl.php page shell provides the
sidebar directly. The body wrapper caused double-rendering."
```

---

## Chunk 2: Account Shell + Menu

### Task 5: Create account-shell.tpl.php

**Files:**
- Create: `Themes/Twenty26/templates/modern/account-shell.tpl.php`

Self-contained page shell for all `/account/` URLs. Same isolation principle as admin-shell, but with a light sidebar instead of dark.

- [ ] **Step 1: Create account-shell.tpl.php**

Create `Themes/Twenty26/templates/modern/account-shell.tpl.php`:

```php
<?php
    $template = $this->formatShellVariables($vars);
    $vars = $template->vars;
    $template->draw('shell/headers');
?>
<!DOCTYPE html>
<html lang="<?= $vars['lang'] ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(\Idno\Core\Idno::site()->config()->getTitle()) ?> — Account</title>
    <link rel="stylesheet" href="<?= \Idno\Core\Idno::site()->config()->getStaticURL() ?>Themes/Twenty26/dist/modern.min.css">
<?php
    // Render any plugin-registered CSS assets
    if ($assets = \Idno\Core\Idno::site()->currentPage()->getAssets('css')) {
        foreach ($assets as $asset) {
            echo '    <link rel="stylesheet" href="' . htmlspecialchars($asset) . '">' . "\n";
        }
    }
?>
</head>
<body class="account-template">
    <div class="idno-account-shell">
        <nav class="idno-account-sidebar">
            <div class="idno-account-header"><?= \Idno\Core\Idno::site()->language()->_('Account') ?></div>
            <?= $this->draw('account/menu') ?>
            <div class="idno-account-back">
                <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>" class="idno-account-nav-item">
                    <?= $this->__(['icon' => 'arrow-left'])->draw('shell/icon') ?>
                    <span><?= \Idno\Core\Idno::site()->language()->_('Back to site') ?></span>
                </a>
            </div>
        </nav>
        <main class="idno-account-main">
            <div class="idno-account-content">
                <?= $template->draw('shell/messages') ?>
                <?php
                    if (!empty($vars['body'])) {
                        echo $vars['body'];
                    }
                ?>
            </div>
        </main>
    </div>
    <script src="<?= \Idno\Core\Idno::site()->config()->getStaticURL() ?>Themes/Twenty26/dist/modern.min.js" defer></script>
<?php
    // Render any plugin-registered JS assets
    if ($assets = \Idno\Core\Idno::site()->currentPage()->getAssets('javascript')) {
        foreach ($assets as $asset) {
            echo '    <script src="' . htmlspecialchars($asset) . '"></script>' . "\n";
        }
    }
    echo $template->draw('shell/form-data');
?>
</body>
</html>
```

- [ ] **Step 2: Commit**

```bash
git add Themes/Twenty26/templates/modern/account-shell.tpl.php
git commit -m "feat: add self-contained account-shell.tpl.php

Complete HTML page shell for account settings pages. Light sidebar
with account menu. Loads CSS/JS directly without drawing from
shell/* subtemplates."
```

---

### Task 6: Create the Twenty26 account/menu.tpl.php

**Files:**
- Create: `Themes/Twenty26/templates/modern/account/menu.tpl.php`

The default account menu uses Bootstrap `<ul class="nav"><li>` markup. This Twenty26 override uses the same nav-item pattern as the admin menu, with icons. Preserves the `account/menu/items` extension point for plugins.

- [ ] **Step 1: Create account/menu.tpl.php**

Create `Themes/Twenty26/templates/modern/account/menu.tpl.php`:

```php
<?php
    /* Account navigation menu for the light sidebar.
     * Preserves account/menu/items extension point for plugin-added menu items.
     */
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
    $page = \Idno\Core\Idno::site()->currentPage();
?>
<div class="idno-account-nav">
    <a href="<?= $baseUrl ?>account/settings/"
       class="idno-account-nav-item<?php if ($page->doesPathMatch('/account/settings/')) echo ' active'; ?>">
        <?= $this->__(['icon' => 'settings'])->draw('shell/icon') ?>
        <span><?= \Idno\Core\Idno::site()->language()->_('Settings') ?></span>
    </a>
    <a href="<?= $baseUrl ?>account/settings/tools/"
       class="idno-account-nav-item<?php if ($page->doesPathMatch('/account/settings/tools/')) echo ' active'; ?>">
        <?= $this->__(['icon' => 'box'])->draw('shell/icon') ?>
        <span><?= \Idno\Core\Idno::site()->language()->_('Tools & Apps') ?></span>
    </a>

    <?= $this->draw('account/menu/items') ?>

    <a href="<?= $baseUrl ?>account/export/"
       class="idno-account-nav-item<?php if ($page->doesPathMatch('/account/export/')) echo ' active'; ?>">
        <?= $this->__(['icon' => 'download'])->draw('shell/icon') ?>
        <span><?= \Idno\Core\Idno::site()->language()->_('Export Data') ?></span>
    </a>
</div>
```

**Icons used:**
- `settings` (gear icon) — for Settings
- `box` (package icon) — for Tools & Apps
- `download` — for Export Data

These all exist in the `shell/icon.tpl.php` icon map.

- [ ] **Step 2: Verify account settings page loads**

Navigate to `https://idno:8890/account/settings/` — you should see the light sidebar with the account menu and the settings form in the content area. The menu items should have icons and correct active states.

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/account/menu.tpl.php
git commit -m "feat: add Twenty26 account/menu.tpl.php with icons

Modern account navigation menu using idno-account-nav-item pattern.
Includes settings, tools, and export links with Lucide icons.
Preserves account/menu/items extension point for plugins."
```

---

## Chunk 3: CSS — Account Sidebar + Plugin Normalization

### Task 7: Add account settings CSS

**Files:**
- Modify: `Themes/Twenty26/src/css/components/admin.css` (append account styles)

Add account-specific CSS classes to the existing `admin.css` file (which already handles admin + settings layout). The account shell uses the same flexbox pattern as admin but with light-theme colors.

- [ ] **Step 1: Add account CSS to admin.css**

Append the following to `Themes/Twenty26/src/css/components/admin.css`, inside a new `@layer components` block at the end of the file (after the existing `@media` block):

```css
/* ── Account Settings Shell ── */
@layer components {
  .idno-account-shell {
    display: flex;
    min-height: 100vh;
    font-family: var(--font-sans);
    font-size: var(--font-size-base);
    color: var(--color-text);
  }

  .idno-account-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 14rem;
    height: 100vh;
    background: var(--color-surface);
    border-right: 1px solid var(--color-border);
    padding: 1.5rem 0;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    z-index: 40;
  }

  .idno-account-header {
    font-size: var(--font-size-lg);
    font-weight: 700;
    color: var(--color-text-strong);
    padding: 0 1.25rem;
    margin-bottom: 1.5rem;
  }

  .idno-account-nav {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
    padding: 0 0.75rem;
    flex: 1;
    list-style: none;
    margin: 0;
  }

  .idno-account-nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.625rem;
    border-radius: var(--radius-sm);
    color: var(--color-text-secondary);
    text-decoration: none;
    font-size: var(--font-size-sm);
    font-weight: 500;
    transition: color 0.15s, background 0.15s;
  }

  .idno-account-nav-item:hover {
    color: var(--color-text-strong);
    background: rgba(0, 0, 0, 0.04);
  }

  .idno-account-nav-item[aria-current="page"],
  .idno-account-nav-item.active {
    color: var(--color-primary);
    background: rgba(0, 0, 0, 0.04);
  }

  .idno-account-nav-item svg {
    width: 1.125rem;
    height: 1.125rem;
    flex-shrink: 0;
  }

  .idno-account-back {
    margin-top: auto;
    padding: 0 0.75rem;
    border-top: 1px solid var(--color-border);
    padding-top: 0.75rem;
  }

  .idno-account-main {
    margin-left: 14rem;
    flex: 1;
    background: var(--color-bg);
    padding: 2rem;
    min-height: 100vh;
  }

  .idno-account-content {
    max-width: 52rem;
  }
}
```

- [ ] **Step 2: Add account responsive CSS**

Append after the account CSS block:

```css
@media (max-width: 768px) {
  @layer components {
    .idno-account-sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: auto;
      flex-direction: row;
      padding: 0.5rem;
      overflow-x: auto;
      border-right: none;
      border-bottom: 1px solid var(--color-border);
    }

    .idno-account-nav {
      flex-direction: row;
      padding: 0;
    }

    .idno-account-main {
      margin-left: 0;
      margin-top: 3.5rem;
      padding: 1rem;
    }

    .idno-account-header {
      display: none;
    }

    .idno-account-back {
      display: none;
    }
  }
}
```

- [ ] **Step 3: Build CSS**

```bash
cd Themes/Twenty26 && npx vite build
```

Verify no build errors.

- [ ] **Step 4: Verify account settings page looks correct**

Navigate to `https://idno:8890/account/settings/` — the light sidebar should have correct spacing, colors, icons, hover states, and active state on the "Settings" item.

- [ ] **Step 5: Commit**

```bash
cd /Users/ben/code/idno
git add Themes/Twenty26/src/css/components/admin.css Themes/Twenty26/dist/
git commit -m "feat: add account settings shell CSS

Light sidebar styles mirroring admin pattern. Includes responsive
breakpoint at 768px converting sidebar to horizontal top bar."
```

---

### Task 8: Add plugin menu item CSS normalization

**Files:**
- Modify: `Themes/Twenty26/src/css/components/admin.css`

Plugins contribute `<li><a>` items via `admin/menu/items` and `account/menu/items` extension points. The Twenty26 navs use `<a class="idno-*-nav-item">` directly. CSS rules normalize the plugin `<li>` items to match.

- [ ] **Step 1: Add plugin normalization CSS**

Append to the admin `@layer components` block in `admin.css` (inside the first `@layer components` block, after `.idno-admin-stat-label`):

```css
  /* Plugin menu item normalization — plugins contribute <li><a> markup */
  .idno-admin-nav li {
    list-style: none;
  }

  .idno-admin-nav li a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.625rem;
    border-radius: var(--radius-sm);
    color: var(--color-admin-text);
    text-decoration: none;
    font-size: var(--font-size-sm);
    font-weight: 500;
    transition: color 0.15s, background 0.15s;
  }

  .idno-admin-nav li a:hover {
    color: var(--color-admin-text-active);
    background: rgba(255, 255, 255, 0.06);
  }

  .idno-admin-nav li.active a {
    color: var(--color-admin-text-active);
    background: rgba(255, 255, 255, 0.08);
  }
```

And append to the account `@layer components` block (after `.idno-account-content`):

```css
  /* Plugin menu item normalization — plugins contribute <li><a> markup */
  .idno-account-nav li {
    list-style: none;
  }

  .idno-account-nav li a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.625rem;
    border-radius: var(--radius-sm);
    color: var(--color-text-secondary);
    text-decoration: none;
    font-size: var(--font-size-sm);
    font-weight: 500;
    transition: color 0.15s, background 0.15s;
  }

  .idno-account-nav li a:hover {
    color: var(--color-text-strong);
    background: rgba(0, 0, 0, 0.04);
  }

  .idno-account-nav li.active a {
    color: var(--color-primary);
    background: rgba(0, 0, 0, 0.04);
  }
```

- [ ] **Step 2: Build CSS**

```bash
cd Themes/Twenty26 && npx vite build
```

- [ ] **Step 3: Verify plugin menu items render correctly**

If you have plugins installed (e.g., ActivityPub), navigate to admin pages and verify their menu entries in the sidebar look consistent with the native nav items — same padding, font size, hover state.

- [ ] **Step 4: Commit**

```bash
cd /Users/ben/code/idno
git add Themes/Twenty26/src/css/components/admin.css Themes/Twenty26/dist/
git commit -m "feat: add CSS normalization for plugin menu items

Style <li><a> markup from plugins to match native nav items in
both admin (dark) and account (light) sidebars."
```

---

## Chunk 4: Plugin Page Styling + Button Variants

### Task 9: Create Twenty26 admin/plugins/plugin.tpl.php

**Files:**
- Create: `Themes/Twenty26/templates/modern/admin/plugins/plugin.tpl.php`

The default plugin template uses Bootstrap grid classes (`col-md-2`, `col-md-5`, `well well-large`). Create a Twenty26 override using the design system classes.

- [ ] **Step 1: Create admin/plugins/plugin.tpl.php**

Create `Themes/Twenty26/templates/modern/admin/plugins/plugin.tpl.php`:

```php
<?php
$plugin_description = $vars['plugin']['Plugin description'];
$shortname = $vars['plugin']['shortname'];
$isEnabled = array_key_exists($shortname, $vars['plugins_loaded']);
$isAlwaysOn = in_array($shortname, \Idno\Core\Idno::site()->config()->alwaysplugins);
?>
<div class="idno-admin-card" id="plugin-<?= strtolower($shortname) ?>">
    <div class="idno-plugin-row">
        <div class="idno-plugin-info">
            <div class="idno-plugin-name">
                <?= htmlspecialchars($plugin_description['name']) ?>
                <span class="idno-plugin-version"><?= htmlspecialchars($plugin_description['version']) ?></span>
            </div>
            <?php if (!empty($plugin_description['author'])) { ?>
            <div class="idno-plugin-author">
                <?= \Idno\Core\Idno::site()->language()->_('by') ?>
                <?php if (!empty($plugin_description['author_url'])) { ?>
                    <a href="<?= htmlspecialchars($plugin_description['author_url']) ?>"><?= htmlspecialchars($plugin_description['author']) ?></a>
                <?php } else { ?>
                    <?= htmlspecialchars($plugin_description['author']) ?>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if (!empty($plugin_description['description'])) { ?>
            <div class="idno-plugin-description">
                <?= htmlspecialchars($plugin_description['description']) ?>
            </div>
            <?php } ?>
        </div>
        <div class="idno-plugin-action">
            <?php if (!$isAlwaysOn) { ?>
                <?php if ($isEnabled) { ?>
                <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/plugins/" method="post">
                    <input type="hidden" name="plugin" value="<?= $shortname ?>">
                    <input type="hidden" name="container" value="plugin-<?= strtolower($shortname) ?>">
                    <input type="hidden" name="plugin_action" value="uninstall">
                    <button type="submit" class="idno-btn idno-btn-sm idno-btn-disable"><?= \Idno\Core\Idno::site()->language()->_('Disable') ?></button>
                    <?= \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/plugins/') ?>
                </form>
                <?php } else { ?>
                <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/plugins/" method="post">
                    <input type="hidden" name="plugin" value="<?= $shortname ?>">
                    <input type="hidden" name="container" value="plugin-<?= strtolower($shortname) ?>">
                    <input type="hidden" name="plugin_action" value="install">
                    <button type="submit" class="idno-btn idno-btn-sm idno-btn-enable"><?= \Idno\Core\Idno::site()->language()->_('Enable') ?></button>
                    <?= \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/plugins/') ?>
                </form>
                <?php } ?>
            <?php } else { ?>
                <span class="idno-plugin-always-on"><?= \Idno\Core\Idno::site()->language()->_('Always on') ?></span>
            <?php } ?>
        </div>
    </div>
</div>
```

- [ ] **Step 2: Commit**

```bash
git add Themes/Twenty26/templates/modern/admin/plugins/plugin.tpl.php
git commit -m "feat: add Twenty26 plugin list item template

Modern plugin row with name/version/author/description and
enable/disable button using design system classes."
```

---

### Task 10: Add plugin list + enable/disable button CSS

**Files:**
- Modify: `Themes/Twenty26/src/css/components/admin.css` (plugin row styles)
- Modify: `Themes/Twenty26/src/css/components/buttons.css` (enable/disable variants)

- [ ] **Step 1: Add plugin row CSS to admin.css**

Add inside the first `@layer components` block in `admin.css` (after the stat styles):

```css
  /* Plugin list */
  .idno-plugin-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
  }

  .idno-plugin-info {
    flex: 1;
    min-width: 0;
  }

  .idno-plugin-name {
    font-weight: 600;
    color: var(--color-text-strong);
  }

  .idno-plugin-version {
    font-weight: 400;
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
  }

  .idno-plugin-author {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    margin-top: 0.125rem;
  }

  .idno-plugin-author a {
    color: var(--color-text-secondary);
    text-decoration: none;
  }

  .idno-plugin-author a:hover {
    text-decoration: underline;
  }

  .idno-plugin-description {
    font-size: var(--font-size-sm);
    color: var(--color-text-secondary);
    margin-top: 0.375rem;
  }

  .idno-plugin-action {
    flex-shrink: 0;
  }

  .idno-plugin-always-on {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    font-style: italic;
  }

  .idno-admin-description {
    color: var(--color-text-secondary);
    margin-bottom: 1.5rem;
  }
```

- [ ] **Step 2: Add enable/disable button variants to buttons.css**

Add inside the `@layer components` block in `buttons.css` (after `.idno-btn-lg`):

```css
  .idno-btn-enable {
    background: #16a34a;
    color: #ffffff;
  }

  .idno-btn-enable:hover:not(:disabled) {
    background: #15803d;
  }

  .idno-btn-disable {
    background: transparent;
    border: 1px solid var(--color-border);
    color: var(--color-text-secondary);
  }

  .idno-btn-disable:hover:not(:disabled) {
    border-color: #dc2626;
    color: #dc2626;
    background: rgba(220, 38, 38, 0.04);
  }
```

- [ ] **Step 3: Build CSS**

```bash
cd Themes/Twenty26 && npx vite build
```

- [ ] **Step 4: Verify plugins page**

Navigate to `https://idno:8890/admin/plugins/` — each plugin should show as a card with name/version/author/description on the left and enable/disable button on the right.

- [ ] **Step 5: Commit**

```bash
cd /Users/ben/code/idno
git add Themes/Twenty26/src/css/components/admin.css \
        Themes/Twenty26/src/css/components/buttons.css \
        Themes/Twenty26/dist/
git commit -m "feat: add plugin list styling and enable/disable button variants

Plugin rows with flex layout. Green enable button, outline disable
button with red hover state."
```

---

## Chunk 5: Cleanup + Final Verification

### Task 11: Delete settings-shell.tpl.php

**Files:**
- Delete: `Themes/Twenty26/templates/modern/settings-shell.tpl.php`

Now that both `admin-shell.tpl.php` and `account-shell.tpl.php` exist and are registered, the shared `settings-shell.tpl.php` is dead code.

- [ ] **Step 1: Delete settings-shell.tpl.php**

```bash
rm Themes/Twenty26/templates/modern/settings-shell.tpl.php
```

- [ ] **Step 2: Verify both admin and account pages still work**

- `https://idno:8890/admin/` — admin dashboard loads with dark sidebar
- `https://idno:8890/account/settings/` — account settings loads with light sidebar

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/settings-shell.tpl.php
git commit -m "cleanup: delete settings-shell.tpl.php

Replaced by admin-shell.tpl.php and account-shell.tpl.php.
No longer referenced by any shell override."
```

---

### Task 12: Final build and comprehensive verification

- [ ] **Step 1: Full CSS build**

```bash
cd Themes/Twenty26 && npx vite build
```

Verify clean build with no errors.

- [ ] **Step 2: Verify all admin pages**

Navigate to each and verify: single sidebar, correct page title, working forms, no double-rendering.

| Page | URL |
|------|-----|
| Dashboard | `https://idno:8890/admin/` |
| Themes | `https://idno:8890/admin/themes/` |
| Plugins | `https://idno:8890/admin/plugins/` |
| Users | `https://idno:8890/admin/users/` |
| Email | `https://idno:8890/admin/email/` |
| Statistics | `https://idno:8890/admin/statistics/` |
| Import | `https://idno:8890/admin/import/` |
| Export | `https://idno:8890/admin/export/` |

- [ ] **Step 3: Verify all account pages**

| Page | URL |
|------|-----|
| Settings | `https://idno:8890/account/settings/` |
| Tools | `https://idno:8890/account/settings/tools/` |
| Export | `https://idno:8890/account/export/` |

- [ ] **Step 4: Verify plugin admin pages work**

If ActivityPub or other plugins with admin pages are installed, navigate to their admin pages and verify they render inside the admin shell with the sidebar.

- [ ] **Step 5: Test mobile responsive layout**

Resize browser to < 768px width and verify:
- Admin sidebar converts to bottom bar
- Account sidebar converts to top horizontal bar
- Content areas are usable

- [ ] **Step 6: Final commit (if any fixes needed)**

If any fixes were needed during verification, commit them with appropriate messages.
