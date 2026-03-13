# Plugin Modern Templates Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create modern template overrides for all 7 stock Idno plugins that have admin/account settings pages, so the Twenty26 theme renders them with the card-based UI, Lucide icons, and no jQuery.

**Architecture:** Each plugin gets a `templates/modern/` directory mirroring its `templates/default/` structure. The Bonita template engine resolves by template type automatically. All interactive behavior uses Alpine.js or vanilla JS — no jQuery.

**Tech Stack:** PHP templates (`.tpl.php`), Alpine.js, vanilla JavaScript, HTML5 Drag and Drop API, existing Twenty26 CSS classes

**Spec:** `docs/superpowers/specs/2026-03-13-plugin-modern-templates-design.md`

---

## Key Patterns Reference

All modern admin templates follow these conventions from the existing Twenty26 theme:

**Page structure:**
```php
<h1 class="idno-admin-page-title">Title</h1>
<p class="idno-admin-description">Description text</p>
<div class="idno-admin-card">
    <h2 class="idno-admin-card-title">Section Title</h2>
    <!-- content -->
</div>
```

**Form elements:**
```php
<div class="idno-form-group">
    <label class="idno-label" for="id">Label</label>
    <input type="text" id="id" name="name" class="idno-input" value="">
    <p class="idno-form-help">Help text</p>
</div>
<button type="submit" class="idno-btn idno-btn-primary">Save</button>
```

**Admin nav items** (injected via `admin/menu/items` extension point):
```php
<?php $page = \Idno\Core\Idno::site()->currentPage(); $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL(); ?>
<a href="<?= $baseUrl ?>admin/example/" class="idno-admin-nav-item<?php if ($page->doesPathMatch('/admin/example/')) echo ' active'; ?>">
    <?= $this->__(['icon' => 'icon-name'])->draw('shell/icon') ?>
    <span><?= \Idno\Core\Idno::site()->language()->_('Label') ?></span>
</a>
```

**Account nav items** use `idno-account-nav-item` class instead of `idno-admin-nav-item`.

---

## Chunk 1: Icon Map + All 8 Navigation Menu Templates

### Task 1: Add `paintbrush` icon to icon map

**Files:**
- Modify: `Themes/Twenty26/templates/modern/shell/icon.tpl.php`

- [ ] **Step 1: Add the paintbrush SVG path to the icons array**

In `Themes/Twenty26/templates/modern/shell/icon.tpl.php`, add the `paintbrush` entry to the `$icons` array in the Admin section, after the `'palette'` entry:

```php
'paintbrush' => '<path d="M18.37 2.63 14 7l-1.59-1.59a2 2 0 0 0-2.82 0L8 7l9 9 1.59-1.59a2 2 0 0 0 0-2.82L17 10l4.37-4.37a2.12 2.12 0 1 0-3-3Z"/><path d="M9 8c-2 3-4 3.5-7 4l8 10c2-1 6-5 6-7"/><path d="M14.5 17.5 4.5 15"/>',
```

- [ ] **Step 2: Verify the icon renders**

Navigate to any Twenty26 admin page in the browser and confirm no rendering errors. The `paintbrush` icon will be used by the Styles plugin menu item in the next task.

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/shell/icon.tpl.php
git commit -m "feat(Twenty26): add paintbrush icon to Lucide icon map"
```

### Task 2: Create all 8 navigation menu templates

**Files:**
- Create: `IdnoPlugins/ActivityPub/templates/modern/activitypub/admin/menu.tpl.php`
- Create: `IdnoPlugins/StaticPages/templates/modern/staticpages/admin/menu.tpl.php`
- Create: `IdnoPlugins/Styles/templates/modern/styles/admin/menu.tpl.php`
- Create: `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/admin/menu.tpl.php`
- Create: `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/account/menu.tpl.php`
- Create: `IdnoPlugins/Webhooks/templates/modern/webhooks/admin/menu.tpl.php`
- Create: `IdnoPlugins/Bridgy/templates/modern/bridgy/menu.tpl.php`
- Create: `IdnoPlugins/IndiePub/templates/modern/account/menu/items/indiepub.tpl.php`

- [ ] **Step 1: Create ActivityPub admin menu**

Create `IdnoPlugins/ActivityPub/templates/modern/activitypub/admin/menu.tpl.php`:

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

- [ ] **Step 2: Create StaticPages admin menu**

Create `IdnoPlugins/StaticPages/templates/modern/staticpages/admin/menu.tpl.php`:

```php
<?php
    $page = \Idno\Core\Idno::site()->currentPage();
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
?>
<a href="<?= $baseUrl ?>admin/staticpages/"
   class="idno-admin-nav-item<?php if ($page->doesPathMatch('/admin/staticpages/')) echo ' active'; ?>">
    <?= $this->__(['icon' => 'file-text'])->draw('shell/icon') ?>
    <span><?= \Idno\Core\Idno::site()->language()->_('Pages') ?></span>
</a>
```

- [ ] **Step 3: Create Styles admin menu**

Create `IdnoPlugins/Styles/templates/modern/styles/admin/menu.tpl.php`:

```php
<?php
    $page = \Idno\Core\Idno::site()->currentPage();
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
?>
<a href="<?= $baseUrl ?>admin/styles/"
   class="idno-admin-nav-item<?php if ($page->doesPathMatch('/admin/styles/')) echo ' active'; ?>">
    <?= $this->__(['icon' => 'paintbrush'])->draw('shell/icon') ?>
    <span><?= \Idno\Core\Idno::site()->language()->_('Custom CSS') ?></span>
</a>
```

- [ ] **Step 4: Create StandardSiteSync admin menu**

Create `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/admin/menu.tpl.php`:

```php
<?php
    $page = \Idno\Core\Idno::site()->currentPage();
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
?>
<a href="<?= $baseUrl ?>admin/standardsitesync/"
   class="idno-admin-nav-item<?php if ($page->doesPathMatch('/admin/standardsitesync/')) echo ' active'; ?>">
    <?= $this->__(['icon' => 'refresh-cw'])->draw('shell/icon') ?>
    <span><?= \Idno\Core\Idno::site()->language()->_('Standard.site Sync') ?></span>
</a>
```

- [ ] **Step 5: Create StandardSiteSync account menu**

Create `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/account/menu.tpl.php`:

```php
<?php
    $page = \Idno\Core\Idno::site()->currentPage();
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
?>
<a href="<?= $baseUrl ?>account/settings/standardsitesync/"
   class="idno-account-nav-item<?php if ($page->doesPathMatch('/account/settings/standardsitesync/')) echo ' active'; ?>">
    <?= $this->__(['icon' => 'refresh-cw'])->draw('shell/icon') ?>
    <span><?= \Idno\Core\Idno::site()->language()->_('Standard.site Sync') ?></span>
</a>
```

- [ ] **Step 6: Create Webhooks admin menu**

Create `IdnoPlugins/Webhooks/templates/modern/webhooks/admin/menu.tpl.php`:

```php
<?php
    $page = \Idno\Core\Idno::site()->currentPage();
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
?>
<a href="<?= $baseUrl ?>admin/webhooks/"
   class="idno-admin-nav-item<?php if ($page->doesPathMatch('/admin/webhooks/')) echo ' active'; ?>">
    <?= $this->__(['icon' => 'webhook'])->draw('shell/icon') ?>
    <span><?= \Idno\Core\Idno::site()->language()->_('Webhooks') ?></span>
</a>
```

- [ ] **Step 7: Create Bridgy account menu**

Create `IdnoPlugins/Bridgy/templates/modern/bridgy/menu.tpl.php`:

```php
<?php
    $page = \Idno\Core\Idno::site()->currentPage();
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
?>
<a href="<?= $baseUrl ?>account/bridgy/"
   class="idno-account-nav-item<?php if ($page->doesPathMatch('/account/bridgy/')) echo ' active'; ?>">
    <?= $this->__(['icon' => 'share-2'])->draw('shell/icon') ?>
    <span><?= \Idno\Core\Idno::site()->language()->_('Interactions') ?></span>
</a>
```

- [ ] **Step 8: Create IndiePub account menu**

Create `IdnoPlugins/IndiePub/templates/modern/account/menu/items/indiepub.tpl.php`:

```php
<?php
    $page = \Idno\Core\Idno::site()->currentPage();
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
?>
<a href="<?= $baseUrl ?>account/indiepub/"
   class="idno-account-nav-item<?php if ($page->doesPathMatch('/account/indiepub/')) echo ' active'; ?>">
    <?= $this->__(['icon' => 'send'])->draw('shell/icon') ?>
    <span><?= \Idno\Core\Idno::site()->language()->_('IndiePub') ?></span>
</a>
```

- [ ] **Step 9: Verify all nav items render in the browser**

Enable each plugin that isn't already enabled. Navigate to `/admin/plugins/` and `/account/settings/` in the Twenty26 theme. Confirm all new menu items appear in the sidebar with their Lucide icons and correct active states.

- [ ] **Step 10: Commit**

```bash
git add IdnoPlugins/ActivityPub/templates/modern/ \
        IdnoPlugins/StaticPages/templates/modern/ \
        IdnoPlugins/Styles/templates/modern/ \
        IdnoPlugins/StandardSiteSync/templates/modern/ \
        IdnoPlugins/Webhooks/templates/modern/ \
        IdnoPlugins/Bridgy/templates/modern/ \
        IdnoPlugins/IndiePub/templates/modern/
git commit -m "feat(plugins): add modern nav menu templates with Lucide icons

All 7 plugins with admin/account pages now provide modern menu
templates with icons for the Twenty26 sidebar navigation."
```

---

## Chunk 2: Simple Admin Pages (No JS / Vanilla JS)

### Task 3: ActivityPub admin page (no JS)

**Files:**
- Create: `IdnoPlugins/ActivityPub/templates/modern/activitypub/admin.tpl.php`

**Reference:** Default template at `IdnoPlugins/ActivityPub/templates/default/activitypub/admin.tpl.php` — uses `$vars['queue_ok']`, `$vars['total_users']`, `$vars['total_followers']`, `$vars['user_stats']` (array of `{name, handle, followers, has_keys}`).

- [ ] **Step 1: Create the modern ActivityPub admin template**

Create `IdnoPlugins/ActivityPub/templates/modern/activitypub/admin.tpl.php`:

```php
<h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('ActivityPub Federation') ?></h1>
<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('ActivityPub federation allows users on this site to be followed from Mastodon and other compatible platforms. When users publish content, it is automatically delivered to their followers on the fediverse.') ?>
</p>

<?php if (empty($vars['queue_ok'])) { ?>
<div class="idno-admin-card" style="border-left:4px solid var(--color-warning,#eab308);background:var(--color-warning-bg,#fefce8)">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Asynchronous queue required') ?></h2>
    <p>
        <?= \Idno\Core\Idno::site()->language()->_('ActivityPub needs the asynchronous event queue to deliver posts and accept follows from other platforms.') ?>
        <?= \Idno\Core\Idno::site()->language()->_('Please see the <a href="https://github.com/idno/idno#setting-up-the-async-pipeline">setup instructions</a> in the README to enable it.') ?>
    </p>
</div>
<?php } ?>

<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Status') ?></h2>
    <div style="display:flex;gap:2rem;margin-top:0.5rem;">
        <div class="idno-admin-stat">
            <div class="idno-admin-stat-value"><?= $vars['total_users'] ?></div>
            <div class="idno-admin-stat-label"><?= \Idno\Core\Idno::site()->language()->_('Total Users') ?></div>
        </div>
        <div class="idno-admin-stat">
            <div class="idno-admin-stat-value"><?= $vars['total_followers'] ?></div>
            <div class="idno-admin-stat-label"><?= \Idno\Core\Idno::site()->language()->_('AP Followers') ?></div>
        </div>
    </div>
</div>

<?php if (!empty($vars['user_stats'])) { ?>
<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Users') ?></h2>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:2px solid var(--color-border,#e5e7eb);">
                <th style="text-align:left;padding:0.5rem 0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('User') ?></th>
                <th style="text-align:left;padding:0.5rem 0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Handle') ?></th>
                <th style="text-align:left;padding:0.5rem 0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('AP Followers') ?></th>
                <th style="text-align:left;padding:0.5rem 0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Keys') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vars['user_stats'] as $stat) { ?>
            <tr style="border-bottom:1px solid var(--color-border,#e5e7eb);">
                <td style="padding:0.5rem 0.5rem;"><?= htmlspecialchars($stat['name']) ?></td>
                <td style="padding:0.5rem 0.5rem;"><code><?= htmlspecialchars($stat['handle']) ?></code></td>
                <td style="padding:0.5rem 0.5rem;"><?= $stat['followers'] ?></td>
                <td style="padding:0.5rem 0.5rem;">
                    <?php if ($stat['has_keys']) { ?>
                        <span style="color:var(--color-success,#16a34a);">✓ <?= \Idno\Core\Idno::site()->language()->_('Generated') ?></span>
                    <?php } else { ?>
                        <span style="color:var(--color-warning,#eab308);">⏳ <?= \Idno\Core\Idno::site()->language()->_('Pending') ?></span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>

<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Configuration') ?></h2>
    <p><?= \Idno\Core\Idno::site()->language()->_('ActivityPub is enabled. To disable it, go to the Plugins page and deactivate the ActivityPub plugin.') ?></p>
    <dl style="margin-top:0.75rem;">
        <div style="display:flex;gap:0.5rem;margin-bottom:0.4rem;">
            <dt style="font-weight:600;min-width:8rem;"><?= \Idno\Core\Idno::site()->language()->_('WebFinger') ?>:</dt>
            <dd><code><?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>.well-known/webfinger</code></dd>
        </div>
        <div style="display:flex;gap:0.5rem;margin-bottom:0.4rem;">
            <dt style="font-weight:600;min-width:8rem;"><?= \Idno\Core\Idno::site()->language()->_('Shared Inbox') ?>:</dt>
            <dd><code><?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>inbox</code></dd>
        </div>
        <div style="display:flex;gap:0.5rem;margin-bottom:0.4rem;">
            <dt style="font-weight:600;min-width:8rem;"><?= \Idno\Core\Idno::site()->language()->_('NodeInfo') ?>:</dt>
            <dd><code><?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>.well-known/nodeinfo</code></dd>
        </div>
    </dl>
</div>
```

- [ ] **Step 2: Verify in browser**

Navigate to `/admin/activitypub/` with Twenty26 theme active. Confirm the page renders with cards, stats, user table, and configuration info. Check warning banner appears if async queue is disabled.

- [ ] **Step 3: Commit**

```bash
git add IdnoPlugins/ActivityPub/templates/modern/activitypub/admin.tpl.php
git commit -m "feat(ActivityPub): add modern admin template"
```

### Task 4: StandardSiteSync admin page (no JS)

**Files:**
- Create: `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/admin/settings.tpl.php`

**Reference:** Default template uses `$vars['sync_enabled']`. Form POSTs to `admin/standardsitesync/` with `sync_enabled` checkbox.

- [ ] **Step 1: Create the modern StandardSiteSync admin template**

Create `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/admin/settings.tpl.php`:

```php
<h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('Standard.site Sync') ?></h1>
<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('Sync your writing to <a href="https://standard.site">standard.site</a> records on an AT Protocol Personal Data Server (PDS). This makes your content discoverable across the AT Protocol network via apps like Leaflet, Frontpage, and others that support the standard.site lexicons.') ?>
</p>

<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Site-Wide Setting') ?></h2>
    <p><?= \Idno\Core\Idno::site()->language()->_('Enable or disable standard.site sync for this site. When enabled, users can connect their own AT Protocol PDS accounts from their account settings.') ?></p>

    <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/standardsitesync/" method="post">
        <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/standardsitesync/') ?>

        <div class="idno-form-group" style="margin-top:1rem;">
            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                <input type="checkbox" name="sync_enabled" value="1" <?php if (!empty($vars['sync_enabled'])) echo 'checked'; ?>>
                <?= \Idno\Core\Idno::site()->language()->_('Enable standard.site sync') ?>
            </label>
        </div>

        <div class="idno-form-actions">
            <button type="submit" class="idno-btn idno-btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Save') ?></button>
        </div>
    </form>
</div>
```

- [ ] **Step 2: Verify in browser**

Navigate to `/admin/standardsitesync/` with the plugin enabled. Confirm card renders, checkbox reflects state, save works.

- [ ] **Step 3: Commit**

```bash
git add IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/admin/settings.tpl.php
git commit -m "feat(StandardSiteSync): add modern admin settings template"
```

### Task 5: Custom CSS / Styles admin page (vanilla JS)

**Files:**
- Create: `IdnoPlugins/Styles/templates/modern/styles/admin.tpl.php`

**Reference:** Default template uses `$vars['css']`. Form POSTs to `admin/styles/` with `css` textarea and `import` file input. Uses `enctype="multipart/form-data"`. The jQuery file input handler `$('#css-filename').html(...)` needs vanilla JS replacement.

- [ ] **Step 1: Create the modern Styles admin template**

Create `IdnoPlugins/Styles/templates/modern/styles/admin.tpl.php`:

```php
<h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('Custom CSS') ?></h1>
<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('The site styles CSS editor lets you easily modify the visual style of your Idno site by overriding the default CSS. With Custom CSS, you have more control over the fonts, colors, and visual impact of your site.') ?>
</p>

<form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/styles/" method="post" enctype="multipart/form-data">
    <div class="idno-admin-card">
        <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Stylesheet editor') ?></h2>

        <p><?= \Idno\Core\Idno::site()->language()->_("Add your changes to Idno's core CSS below.") ?></p>

        <div class="idno-form-group">
            <label class="idno-label" style="display:inline-flex;align-items:center;gap:0.5rem;cursor:pointer;padding:0.4rem 0.8rem;border:1px solid var(--color-border,#d1d5db);border-radius:6px;font-size:0.875rem;">
                <span id="css-filename"><?= \Idno\Core\Idno::site()->language()->_('Upload a stylesheet') ?></span>
                <input type="file" name="import" accept="text/css" id="cssfileinput" style="display:none;">
            </label>
            <p class="idno-form-help"><?= \Idno\Core\Idno::site()->language()->_("Do you have an existing stylesheet that you'd like to use? Import a CSS file from your computer.") ?></p>
        </div>

        <div class="idno-form-group">
            <textarea name="css" class="idno-input" style="height:20em;font-family:'SF Mono','Fira Code',Courier,monospace;font-size:0.8125rem;resize:vertical;"><?= htmlspecialchars($vars['css']) ?></textarea>
        </div>

        <p style="font-size:0.875rem;">
            <?= \Idno\Core\Idno::site()->language()->_('You can also') ?>
            <a href="<?= \Idno\Core\Idno::site()->config()->url ?>styles/site/"><?= \Idno\Core\Idno::site()->language()->_('download your stylesheet') ?></a>
            <?= \Idno\Core\Idno::site()->language()->_('to work on it locally.') ?>
        </p>
    </div>

    <div class="idno-form-actions" style="margin-top:var(--spacing-section,1.5rem)">
        <button type="submit" class="idno-btn idno-btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Save stylesheet') ?></button>
    </div>

    <?= \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/styles/') ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('cssfileinput');
    var filenameLabel = document.getElementById('css-filename');
    if (fileInput && filenameLabel) {
        fileInput.addEventListener('change', function() {
            filenameLabel.textContent = this.files.length ? this.files[0].name : '<?= \Idno\Core\Idno::site()->language()->_('Upload a stylesheet') ?>';
        });
    }
});
</script>
```

- [ ] **Step 2: Verify in browser**

Navigate to `/admin/styles/`. Confirm textarea shows current CSS, file upload label updates when a file is selected, save submits the form correctly.

- [ ] **Step 3: Commit**

```bash
git add IdnoPlugins/Styles/templates/modern/styles/admin.tpl.php
git commit -m "feat(Styles): add modern Custom CSS admin template"
```

---

## Chunk 3: Account Pages (Vanilla JS / Simple Alpine)

### Task 6: StandardSiteSync account page (vanilla JS — details/summary)

**Files:**
- Create: `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/account/settings.tpl.php`

**Reference:** Default template uses `$vars['sync_enabled']`, `$vars['connected']`, `$vars['did']`, `$vars['pds']`. Three states: sync disabled, connected, not connected. Form actions: connect POST to `account/settings/standardsitesync/`, disconnect POST to `account/settings/standardsitesync/disconnect/`, backfill POST to `account/settings/standardsitesync/backfill/`.

- [ ] **Step 1: Create the modern StandardSiteSync account template**

Create `IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/account/settings.tpl.php`:

```php
<h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('Standard.site Sync') ?></h1>
<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('Automatically share your writing with the wider web. When you connect your account, everything you publish here will also appear on apps like <a href="https://leaflet.pub">Leaflet</a>, <a href="https://frontpage.fyi">Frontpage</a>, and other readers that support <a href="https://standard.site">standard.site</a>.') ?>
</p>
<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('Your content stays on your site &mdash; syncing just makes it discoverable in more places.') ?>
</p>

<?php if (empty($vars['sync_enabled'])) { ?>

<div class="idno-admin-card">
    <p><?= \Idno\Core\Idno::site()->language()->_('Syncing is not currently available. A site administrator needs to enable it before you can connect.') ?></p>
</div>

<?php } elseif (!empty($vars['connected'])) { ?>

<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('You\'re connected!') ?></h2>
    <p><?= \Idno\Core\Idno::site()->language()->_('New posts you publish will automatically be synced. You don\'t need to do anything else.') ?></p>

    <details style="margin-top:1rem;">
        <summary style="cursor:pointer;font-size:0.875rem;color:var(--color-muted,#6b7280);"><?= \Idno\Core\Idno::site()->language()->_('Show technical details') ?></summary>
        <dl style="margin-top:0.5rem;font-size:0.875rem;">
            <div style="display:flex;gap:0.5rem;margin-bottom:0.25rem;">
                <dt style="font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Your identity') ?>:</dt>
                <dd><code><?= htmlspecialchars($vars['did']) ?></code></dd>
            </div>
            <div style="display:flex;gap:0.5rem;">
                <dt style="font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Server') ?>:</dt>
                <dd><code><?= htmlspecialchars($vars['pds']) ?></code></dd>
            </div>
        </dl>
    </details>

    <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/standardsitesync/disconnect/" method="post" style="margin-top:1.5rem;">
        <?= \Idno\Core\Idno::site()->actions()->signForm('/account/settings/standardsitesync/disconnect/') ?>
        <button type="submit" class="idno-btn" style="color:var(--color-danger,#dc2626);border-color:var(--color-danger,#dc2626);"
                onclick="return confirm('<?= \Idno\Core\Idno::site()->language()->_('Are you sure you want to disconnect? Your posts will stop syncing.') ?>');">
            <?= \Idno\Core\Idno::site()->language()->_('Disconnect') ?>
        </button>
    </form>
</div>

<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Sync older posts') ?></h2>
    <p><?= \Idno\Core\Idno::site()->language()->_('Already have posts on this site? You can sync them all at once. Posts that have already been synced will be skipped, so it\'s safe to run this more than once.') ?></p>

    <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/standardsitesync/backfill/" method="post" style="margin-top:1rem;">
        <?= \Idno\Core\Idno::site()->actions()->signForm('/account/settings/standardsitesync/backfill/') ?>
        <button type="submit" class="idno-btn idno-btn-primary"
                onclick="return confirm('<?= \Idno\Core\Idno::site()->language()->_('This will sync all your existing posts. Continue?') ?>');">
            <?= \Idno\Core\Idno::site()->language()->_('Sync all my posts') ?>
        </button>
    </form>
</div>

<?php } else { ?>

<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Get started') ?></h2>
    <p><?= \Idno\Core\Idno::site()->language()->_('To start syncing, enter the handle for your AT Protocol account (this is usually your Bluesky handle, like <strong>yourname.bsky.social</strong>). You\'ll be taken to your server to approve the connection, then brought back here.') ?></p>

    <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/standardsitesync/" method="post" style="margin-top:1rem;">
        <?= \Idno\Core\Idno::site()->actions()->signForm('/account/settings/standardsitesync/') ?>
        <input type="hidden" name="action" value="connect">

        <div class="idno-form-group">
            <label class="idno-label" for="atproto-handle"><?= \Idno\Core\Idno::site()->language()->_('Your handle') ?></label>
            <div style="display:flex;gap:0.5rem;align-items:start;">
                <input type="text" id="atproto-handle" name="handle" placeholder="yourname.bsky.social" class="idno-input" style="max-width:20rem;" required>
                <button type="submit" class="idno-btn idno-btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Connect') ?></button>
            </div>
            <p class="idno-form-help"><?= \Idno\Core\Idno::site()->language()->_('Don\'t have one? You can create a free account at <a href="https://bsky.app">bsky.app</a> or any AT Protocol provider.') ?></p>
        </div>
    </form>
</div>

<?php } ?>
```

- [ ] **Step 2: Verify in browser**

Navigate to `/account/settings/standardsitesync/`. Test all three states: sync disabled (by toggling the admin setting), not connected, and connected. Verify technical details disclosure works, confirm dialogs appear.

- [ ] **Step 3: Commit**

```bash
git add IdnoPlugins/StandardSiteSync/templates/modern/standardsitesync/account/settings.tpl.php
git commit -m "feat(StandardSiteSync): add modern account settings template"
```

### Task 7: Bridgy account page (vanilla JS)

**Files:**
- Create: `IdnoPlugins/Bridgy/templates/modern/bridgy/account.tpl.php`

**Reference:** Default template uses `$vars['twitter_enabled']`, `$vars['twitter_key']`. Forms POST externally to `brid.gy`. On page load, jQuery polls `/account/bridgy/check/` for status changes.

- [ ] **Step 1: Create the modern Bridgy account template**

Create `IdnoPlugins/Bridgy/templates/modern/bridgy/account.tpl.php`:

```php
<h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('Social Interactions') ?></h1>
<p class="idno-admin-description">
    <a href="https://www.brid.gy"><?= \Idno\Core\Idno::site()->language()->_('Bridgy') ?></a>
    <?= \Idno\Core\Idno::site()->language()->_('is a service that pulls social interactions - such as likes and retweets - back to your website.') ?>
</p>
<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('If you send content from Idno to Facebook or Twitter, use Bridgy to save comments and interactions from those networks to the original post on your Idno site.') ?>
</p>

<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Twitter + Bridgy') ?></h2>

    <?php if ($vars['twitter_enabled']) { ?>
        <p><?= \Idno\Core\Idno::site()->language()->_('Bridgy is pulling in replies, favorites, and retweets from Twitter. Click to disable.') ?></p>
        <form action="https://www.brid.gy/delete/start" method="post" style="margin-top:1rem;">
            <input type="hidden" name="feature" value="listen">
            <input type="hidden" name="key" value="<?= $vars['twitter_key'] ?>">
            <input type="hidden" name="callback" value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/bridgy/disabled/?service=twitter' ?>">
            <button type="submit" class="idno-btn" style="color:var(--color-danger,#dc2626);border-color:var(--color-danger,#dc2626);">
                <?= \Idno\Core\Idno::site()->language()->_('Disconnect Twitter + Bridgy') ?>
            </button>
        </form>
    <?php } else { ?>
        <p><?= \Idno\Core\Idno::site()->language()->_('Bridgy pulls in replies, favorites, and retweets from Twitter.') ?></p>
        <p><?= \Idno\Core\Idno::site()->language()->_('To get started, activate Bridgy for the social network.') ?></p>
        <form action="https://www.brid.gy/twitter/start" method="post" style="margin-top:1rem;">
            <input type="hidden" name="feature" value="listen">
            <input type="hidden" name="callback" value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/bridgy/enabled/?service=twitter' ?>">
            <input type="hidden" name="user_url" value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>">
            <button type="submit" class="idno-btn idno-btn-primary">
                <?= \Idno\Core\Idno::site()->language()->_('Activate Twitter + Bridgy') ?>
            </button>
        </form>
    <?php } ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/bridgy/check/', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.changed) {
            window.location.reload();
        }
    })
    .catch(function() { /* silently ignore check failures */ });
});
</script>
```

- [ ] **Step 2: Verify in browser**

Navigate to `/account/bridgy/`. Confirm the card shows the correct connect/disconnect state. Verify the status check runs on page load (check Network tab for the `/account/bridgy/check/` request).

- [ ] **Step 3: Commit**

```bash
git add IdnoPlugins/Bridgy/templates/modern/bridgy/account.tpl.php
git commit -m "feat(Bridgy): add modern account template with vanilla JS status check"
```

### Task 8: IndiePub account page (Alpine.js)

**Files:**
- Create: `IdnoPlugins/IndiePub/templates/modern/account/indiepub.tpl.php`

**Reference:** Default template uses `$user->indieauth_tokens` (array keyed by token string, values have `client_id`, `issued_at`, `scope`, `redirect_uri`). Revoke POSTs to `account/indiepub/revoke`. Add POSTs to `account/indiepub/add`.

- [ ] **Step 1: Create the modern IndiePub account template**

Create `IdnoPlugins/IndiePub/templates/modern/account/indiepub.tpl.php`:

```php
<?php
    use Idno\Core\Idno;
    $baseURL = Idno::site()->config()->getDisplayURL();
    $user = Idno::site()->session()->currentUser();
?>
<h1 class="idno-admin-page-title"><?= Idno::site()->language()->_('Micropub Accounts') ?></h1>
<p class="idno-admin-description">
    <?= Idno::site()->language()->_('Manage third-party apps authorized to post to your site via Micropub.') ?>
</p>

<?php if (empty($user->indieauth_tokens)) { ?>
<div class="idno-admin-card">
    <p><?= Idno::site()->language()->_('There are currently no micropub accounts associated with this site.') ?></p>
</div>
<?php } else { ?>
    <?php foreach ((array)$user->indieauth_tokens as $token => $details) { ?>
    <div class="idno-admin-card">
        <h2 class="idno-admin-card-title">
            <a href="<?= htmlspecialchars($details['client_id']) ?>" target="_blank"><?= htmlspecialchars($details['client_id']) ?></a>
        </h2>
        <p style="font-size:0.875rem;color:var(--color-muted,#6b7280);">
            <?= Idno::site()->language()->_('Authorized') ?>
            <strong><?= date('Y-m-d', $details['issued_at']) ?></strong>
            <?= Idno::site()->language()->_('with the scope') ?>
            <strong><?= htmlspecialchars($details['scope']) ?></strong>
        </p>
        <p style="font-size:0.875rem;color:var(--color-muted,#6b7280);margin-top:0.25rem;">
            <?= Idno::site()->language()->_('Redirect URI') ?>: <?= htmlspecialchars($details['redirect_uri']) ?>
        </p>
        <form action="<?= $baseURL ?>account/indiepub/revoke" method="POST" style="margin-top:0.75rem;">
            <input name="token" type="hidden" value="<?= htmlspecialchars($token) ?>">
            <button type="submit" class="idno-btn" style="color:var(--color-danger,#dc2626);border-color:var(--color-danger,#dc2626);"
                    onclick="return confirm('<?= Idno::site()->language()->_('Are you sure you want to revoke this token?') ?>');">
                <?= Idno::site()->language()->_('Revoke Access') ?>
            </button>
            <?= Idno::site()->actions()->signForm('account/indiepub/revoke') ?>
        </form>
    </div>
    <?php } ?>
<?php } ?>

<div x-data="{ showAddForm: false }">
    <p style="margin-top:1rem;">
        <a href="#" @click.prevent="showAddForm = !showAddForm" style="font-size:0.875rem;">
            <?= Idno::site()->language()->_('Add Micropub Account') ?>
        </a>
    </p>

    <div class="idno-admin-card" x-show="showAddForm" x-cloak style="margin-top:0.75rem;">
        <h2 class="idno-admin-card-title"><?= Idno::site()->language()->_('Add Micropub Account') ?></h2>
        <p><?= Idno::site()->language()->_('To manually add a micropub client account and generate an API token, enter the details below.') ?></p>

        <form action="<?= $baseURL ?>account/indiepub/add" method="post" style="margin-top:0.75rem;">
            <div class="idno-form-group">
                <label class="idno-label" for="indiepub-client-id"><?= Idno::site()->language()->_('Client ID') ?></label>
                <input type="text" id="indiepub-client-id" name="client_id" class="idno-input" required>
            </div>
            <div class="idno-form-group">
                <label class="idno-label" for="indiepub-redirect-uri"><?= Idno::site()->language()->_('Redirect URI') ?></label>
                <input type="text" id="indiepub-redirect-uri" name="redirect_uri" class="idno-input" required>
            </div>
            <div class="idno-form-actions">
                <button type="submit" class="idno-btn idno-btn-primary"><?= Idno::site()->language()->_('Save') ?></button>
            </div>
            <?= Idno::site()->actions()->signForm('account/indiepub/add') ?>
        </form>
    </div>
</div>
```

- [ ] **Step 2: Verify in browser**

Navigate to `/account/indiepub/`. Confirm the "Add Micropub Account" toggle shows/hides the form. If tokens exist, verify they display correctly with revoke buttons.

- [ ] **Step 3: Commit**

```bash
git add IdnoPlugins/IndiePub/templates/modern/account/indiepub.tpl.php
git commit -m "feat(IndiePub): add modern account template with Alpine.js toggle"
```

---

## Chunk 4: Interactive Admin Pages (Alpine.js)

### Task 9: Webhooks admin page (Alpine.js)

**Files:**
- Create: `IdnoPlugins/Webhooks/templates/modern/webhooks/admin/home.tpl.php`

**Reference:** Default template reads existing webhooks from `\Idno\Core\Idno::site()->config()->webhook_syndication`. Form POSTs to empty action (current page). Uses `titles[]` and `webhooks[]` input names. jQuery clones a hidden template to add rows; jQuery removes rows via `.closest('.row').remove()`.

- [ ] **Step 1: Create the modern Webhooks admin template**

Create `IdnoPlugins/Webhooks/templates/modern/webhooks/admin/home.tpl.php`:

```php
<h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('Webhooks') ?></h1>
<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('Webhooks let you syndicate content to external applications very simply. The content of your post is sent to an external URL. Services like Slack, Wufoo, and Mailchimp all use Webhooks.') ?>
</p>

<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('About Webhooks') ?></h2>
    <p><?= \Idno\Core\Idno::site()->language()->_('When content is syndicated via Webhooks, the external URL is sent the following data') ?>:</p>
    <ul style="margin-top:0.5rem;padding-left:1.25rem;font-size:0.875rem;">
        <li><strong><?= \Idno\Core\Idno::site()->language()->_('text') ?></strong>: <?= \Idno\Core\Idno::site()->language()->_('the text of the update') ?></li>
        <li><strong><?= \Idno\Core\Idno::site()->language()->_('username') ?></strong>: <?= \Idno\Core\Idno::site()->language()->_('the username of the account-holder') ?></li>
        <li><strong>icon_url</strong>: <?= \Idno\Core\Idno::site()->language()->_("the URL of the user's icon") ?></li>
        <li><strong>content_type</strong>: <?= \Idno\Core\Idno::site()->language()->_('the type of content being sent') ?></li>
    </ul>
</div>

<?php
    // Prepare initial webhooks data for Alpine
    $existingWebhooks = [];
    if (!empty(\Idno\Core\Idno::site()->config()->webhook_syndication)) {
        foreach (\Idno\Core\Idno::site()->config()->webhook_syndication as $webhook) {
            if (!empty($webhook['title']) || !empty($webhook['url'])) {
                $existingWebhooks[] = [
                    'title' => $webhook['title'] ?? '',
                    'url' => $webhook['url'] ?? ''
                ];
            }
        }
    }
    // Always have at least one empty row
    if (empty($existingWebhooks)) {
        $existingWebhooks[] = ['title' => '', 'url' => ''];
    }
?>

<div class="idno-admin-card" x-data="{ webhooks: <?= htmlspecialchars(json_encode($existingWebhooks), ENT_QUOTES) ?> }">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Your Webhooks') ?></h2>

    <form action="" method="post">
        <template x-for="(wh, index) in webhooks" :key="index">
            <div style="display:flex;gap:0.5rem;align-items:start;margin-bottom:0.75rem;">
                <div style="flex:1;">
                    <input type="text" :name="'titles[]'" x-model="wh.title"
                           placeholder="<?= \Idno\Core\Idno::site()->language()->_('Name of this webhook') ?>"
                           class="idno-input" style="width:100%;">
                </div>
                <div style="flex:2;">
                    <input type="text" :name="'webhooks[]'" x-model="wh.url"
                           placeholder="<?= \Idno\Core\Idno::site()->language()->_('Webhook URL') ?>"
                           class="idno-input" style="width:100%;">
                </div>
                <button type="button" @click="webhooks.splice(index, 1)" class="idno-btn"
                        style="color:var(--color-danger,#dc2626);border-color:var(--color-danger,#dc2626);flex-shrink:0;"
                        x-show="webhooks.length > 1">
                    ✕
                </button>
            </div>
        </template>

        <p style="margin-top:0.5rem;">
            <a href="#" @click.prevent="webhooks.push({title:'', url:''})" style="font-size:0.875rem;">
                + <?= \Idno\Core\Idno::site()->language()->_('Add another Webhook') ?>
            </a>
        </p>

        <div class="idno-form-actions" style="margin-top:1rem;">
            <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/webhooks/') ?>
            <button type="submit" class="idno-btn idno-btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Save Webhooks') ?></button>
        </div>
    </form>
</div>
```

- [ ] **Step 2: Verify in browser**

Navigate to `/admin/webhooks/`. Confirm:
- Existing webhooks are pre-populated
- "Add another Webhook" adds a new row
- Remove button (✕) removes a row
- Save submits correctly (check the POST data in Network tab has `titles[]` and `webhooks[]`)

- [ ] **Step 3: Commit**

```bash
git add IdnoPlugins/Webhooks/templates/modern/webhooks/admin/home.tpl.php
git commit -m "feat(Webhooks): add modern admin template with Alpine.js dynamic rows"
```

### Task 10: StaticPages admin page (Alpine.js + HTML5 DnD)

**Files:**
- Create: `IdnoPlugins/StaticPages/templates/modern/staticpages/admin.tpl.php`

**Reference:** Default template uses `$vars['pages']` (associative array keyed by category name, values are arrays of page objects). Each page object has `getID()`, `getTitle()`, `getURL()`, `isHomepage()`, `getClearHomepageURL()`, `getSetAsHomepageURL()`, `getDeleteURL()`. Uses `\Idno\Core\Idno::site()->actions()->createLink()` for delete/homepage actions with CSRF. Category add form POSTs to `admin/staticpages/add/`. Category edit form POSTs to `admin/staticpages/edit/`. Category delete via `createLink()` to `admin/staticpages/delete/`. Reorder: `admin/staticpages/reorder/` (categories) and `admin/staticpages/reorder/page` (pages).

- [ ] **Step 1: Create the modern StaticPages admin template**

Create `IdnoPlugins/StaticPages/templates/modern/staticpages/admin.tpl.php`:

```php
<?php
    $baseUrl = \Idno\Core\Idno::site()->config()->getDisplayURL();
    $categories = [];
?>

<div style="display:flex;justify-content:space-between;align-items:center;">
    <h1 class="idno-admin-page-title"><?= \Idno\Core\Idno::site()->language()->_('Pages') ?></h1>
    <a href="<?= $baseUrl ?>staticpages/edit/" class="idno-btn idno-btn-primary">
        + <?= \Idno\Core\Idno::site()->language()->_('Add new page') ?>
    </a>
</div>
<p class="idno-admin-description">
    <?= \Idno\Core\Idno::site()->language()->_('Pages are a great way to add content to your site that you want to keep separate from your stream of normal posts and updates. Common examples of pages include an about page, a contact page, or a resume.') ?>
</p>

<?php if (!empty($vars['pages'])) {
    foreach ($vars['pages'] as $category => $pages) {
        $categories[$category] = count($pages);
    }
?>

<div class="idno-admin-card">
    <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('All Pages') ?></h2>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:2px solid var(--color-border,#e5e7eb);">
                <th style="width:30px;padding:0.5rem;"></th>
                <th style="text-align:left;padding:0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Title') ?></th>
                <th style="text-align:left;padding:0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Category') ?></th>
                <th style="text-align:right;padding:0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Actions') ?></th>
            </tr>
        </thead>
        <?php foreach ($vars['pages'] as $category => $pages) {
            if (!empty($pages)) { ?>
        <tbody class="sortable-pages" data-category="<?= htmlspecialchars($category) ?>">
            <?php foreach ($pages as $page) { ?>
            <tr draggable="true" data-page-id="<?= $page->getID() ?>" style="border-bottom:1px solid var(--color-border,#e5e7eb);cursor:grab;"
                ondragstart="this.style.opacity='0.4'; event.dataTransfer.setData('text/plain', this.dataset.pageId); event.dataTransfer.effectAllowed='move';"
                ondragend="this.style.opacity='1';"
                ondragover="event.preventDefault(); this.style.borderTop='2px solid var(--color-primary,#2563eb)';"
                ondragleave="this.style.borderTop='';"
                ondrop="event.preventDefault(); this.style.borderTop=''; handlePageDrop(event, this);">
                <td style="padding:0.5rem;color:var(--color-muted,#9ca3af);cursor:grab;">⋮⋮</td>
                <td style="padding:0.5rem;">
                    <a href="<?= $page->getURL() ?>"><?= htmlspecialchars($page->getTitle()) ?></a>
                </td>
                <td style="padding:0.5rem;font-size:0.875rem;color:var(--color-muted,#6b7280);"><?= htmlspecialchars($category) ?></td>
                <td style="padding:0.5rem;text-align:right;font-size:0.875rem;white-space:nowrap;">
                    <?php if ($page->isHomepage()) {
                        echo \Idno\Core\Idno::site()->actions()->createLink(
                            $page->getClearHomepageURL(),
                            '🏠',
                            [],
                            ['method' => 'POST', 'title' => \Idno\Core\Idno::site()->language()->_('Remove as homepage'), 'confirm' => true, 'confirm-text' => \Idno\Core\Idno::site()->language()->_('Are you sure you want to remove this page from your homepage?')]
                        );
                    } else { ?>
                        <span style="opacity:0.3;">
                        <?php echo \Idno\Core\Idno::site()->actions()->createLink(
                            $page->getSetAsHomepageURL(),
                            '🏠',
                            [],
                            ['method' => 'POST', 'title' => \Idno\Core\Idno::site()->language()->_('Make homepage'), 'confirm' => true, 'confirm-text' => \Idno\Core\Idno::site()->language()->_('Are you sure you want to make this page your homepage?')]
                        ); ?>
                        </span>
                    <?php } ?>
                    <a href="<?= $baseUrl ?>staticpage/edit/<?= $page->_id ?>" style="margin-left:0.5rem;" title="<?= \Idno\Core\Idno::site()->language()->_('Edit page') ?>">
                        <?= $this->__(['icon' => 'pencil'])->draw('shell/icon') ?>
                    </a>
                    <span style="margin-left:0.5rem;color:var(--color-danger,#dc2626);">
                    <?= \Idno\Core\Idno::site()->actions()->createLink(
                        $page->getDeleteURL(),
                        $this->__(['icon' => 'trash-2'])->draw('shell/icon'),
                        [],
                        ['method' => 'POST', 'title' => \Idno\Core\Idno::site()->language()->_('Delete page'), 'confirm' => true, 'confirm-text' => \Idno\Core\Idno::site()->language()->_('Are you sure you want to permanently delete this page?')]
                    ) ?>
                    </span>
                </td>
            </tr>
            <?php } ?>
        </tbody>
        <?php }
        } ?>
    </table>
</div>

<?php } ?>

<!-- Categories Section -->
<div class="idno-admin-card" x-data="{ showAddCategory: false }">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Categories') ?></h2>
        <button type="button" @click="showAddCategory = !showAddCategory" class="idno-btn" style="font-size:0.875rem;">
            + <?= \Idno\Core\Idno::site()->language()->_('Add category') ?>
        </button>
    </div>
    <p style="font-size:0.875rem;color:var(--color-muted,#6b7280);">
        <?= \Idno\Core\Idno::site()->language()->_('If you plan on adding many pages, you may want to group them under categories. However, you don\'t have to assign a page to a category.') ?>
    </p>

    <div x-show="showAddCategory" x-cloak style="margin-top:0.75rem;">
        <form action="<?= $baseUrl ?>admin/staticpages/add/" method="post" style="display:flex;gap:0.5rem;align-items:start;">
            <input type="text" name="category" placeholder="<?= \Idno\Core\Idno::site()->language()->_('Name of category to add') ?>" class="idno-input" style="max-width:20rem;" required>
            <button type="submit" class="idno-btn idno-btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Add') ?></button>
            <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/staticpages/add') ?>
        </form>
    </div>

    <?php if (!empty($categories)) { ?>
    <table style="width:100%;border-collapse:collapse;margin-top:1rem;">
        <thead>
            <tr style="border-bottom:2px solid var(--color-border,#e5e7eb);">
                <th style="width:30px;padding:0.5rem;"></th>
                <th style="text-align:left;padding:0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Category Name') ?></th>
                <th style="text-align:left;padding:0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Pages') ?></th>
                <th style="text-align:right;padding:0.5rem;font-weight:600;"><?= \Idno\Core\Idno::site()->language()->_('Actions') ?></th>
            </tr>
        </thead>
        <tbody id="sortable-categories">
            <?php foreach ($categories as $category => $count) {
                $uniqueId = md5($category . rand(0, 999));
                $isNoCategory = ($category === 'No Category');
            ?>
            <tr <?php if (!$isNoCategory) { ?>draggable="true" data-category="<?= htmlspecialchars($category) ?>"
                style="border-bottom:1px solid var(--color-border,#e5e7eb);cursor:grab;"
                ondragstart="this.style.opacity='0.4'; event.dataTransfer.setData('text/plain', this.dataset.category); event.dataTransfer.effectAllowed='move';"
                ondragend="this.style.opacity='1';"
                ondragover="event.preventDefault(); this.style.borderTop='2px solid var(--color-primary,#2563eb)';"
                ondragleave="this.style.borderTop='';"
                ondrop="event.preventDefault(); this.style.borderTop=''; handleCategoryDrop(event, this);"
            <?php } else { ?>style="border-bottom:1px solid var(--color-border,#e5e7eb);"<?php } ?>
                x-data="{ editing: false }">
                <td style="padding:0.5rem;color:var(--color-muted,#9ca3af);"><?php if (!$isNoCategory) echo '⋮⋮'; ?></td>
                <td style="padding:0.5rem;">
                    <span x-show="!editing"><?= htmlspecialchars($category) ?></span>
                    <?php if (!$isNoCategory) { ?>
                    <form x-show="editing" x-cloak action="<?= $baseUrl ?>admin/staticpages/edit/" method="post" style="display:flex;gap:0.5rem;">
                        <input type="text" name="new_category" value="<?= htmlspecialchars($category) ?>" class="idno-input" style="width:auto;">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                        <button type="submit" class="idno-btn idno-btn-primary" style="font-size:0.75rem;"><?= \Idno\Core\Idno::site()->language()->_('Save') ?></button>
                        <button type="button" @click="editing = false" class="idno-btn" style="font-size:0.75rem;"><?= \Idno\Core\Idno::site()->language()->_('Cancel') ?></button>
                        <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/staticpages/edit') ?>
                    </form>
                    <?php } ?>
                </td>
                <td style="padding:0.5rem;"><?= $count ?></td>
                <td style="padding:0.5rem;text-align:right;font-size:0.875rem;">
                    <?php if (!$isNoCategory) { ?>
                        <a href="#" @click.prevent="editing = true" style="margin-right:0.5rem;">
                            <?= $this->__(['icon' => 'pencil'])->draw('shell/icon') ?>
                        </a>
                        <span style="color:var(--color-danger,#dc2626);">
                        <?= \Idno\Core\Idno::site()->actions()->createLink(
                            $baseUrl . 'admin/staticpages/delete/',
                            $this->__(['icon' => 'trash-2'])->draw('shell/icon'),
                            ['category' => $category],
                            ['method' => 'POST', 'confirm' => true, 'confirm-text' => \Idno\Core\Idno::site()->language()->_('Are you sure you want to permanently delete this category?')]
                        ) ?>
                        </span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</div>

<script>
function handlePageDrop(event, targetRow) {
    var draggedId = event.dataTransfer.getData('text/plain');
    var tbody = targetRow.closest('tbody');
    var draggedRow = tbody.querySelector('[data-page-id="' + draggedId + '"]');
    if (!draggedRow || draggedRow === targetRow) return;
    tbody.insertBefore(draggedRow, targetRow);
    var rows = tbody.querySelectorAll('[data-page-id]');
    var newIndex = Array.prototype.indexOf.call(rows, draggedRow);
    fetch('<?= $baseUrl ?>admin/staticpages/reorder/page', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ page: draggedId, position: newIndex })
    });
}

function handleCategoryDrop(event, targetRow) {
    var draggedCategory = event.dataTransfer.getData('text/plain');
    var tbody = targetRow.closest('tbody');
    var draggedRow = tbody.querySelector('[data-category="' + draggedCategory + '"]');
    if (!draggedRow || draggedRow === targetRow) return;
    tbody.insertBefore(draggedRow, targetRow);
    var rows = tbody.querySelectorAll('[data-category]');
    var newIndex = Array.prototype.indexOf.call(rows, draggedRow);
    fetch('<?= $baseUrl ?>admin/staticpages/reorder/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ category: draggedCategory, position: newIndex })
    });
}
</script>
```

- [ ] **Step 2: Verify in browser**

Navigate to `/admin/staticpages/`. Confirm:
- Pages table renders with all pages grouped by category
- Drag handles (⋮⋮) allow reordering pages within a category
- Homepage toggle, edit, and delete actions work
- "Add new page" button links to edit page
- Categories section shows add/edit/delete functionality
- Category drag-to-reorder works
- Inline category edit form appears with Alpine.js toggle

- [ ] **Step 3: Commit**

```bash
git add IdnoPlugins/StaticPages/templates/modern/staticpages/admin.tpl.php
git commit -m "feat(StaticPages): add modern admin template with DnD reorder and Alpine.js"
```

---

## Chunk 5: Final Verification

### Task 11: End-to-end verification

- [ ] **Step 1: Enable all 7 plugins**

Navigate to `/admin/plugins/` and enable: ActivityPub, StaticPages, Styles, StandardSiteSync, Webhooks, Bridgy, IndiePub.

- [ ] **Step 2: Verify all admin nav items**

Navigate to `/admin/`. Confirm all 5 admin plugin nav items appear in the sidebar with correct icons: ActivityPub (globe), Pages (file-text), Custom CSS (paintbrush), Standard.site Sync (refresh-cw), Webhooks (webhook).

- [ ] **Step 3: Verify all account nav items**

Navigate to `/account/settings/`. Confirm all 3 account plugin nav items appear: Standard.site Sync (refresh-cw), Interactions (share-2), IndiePub (send).

- [ ] **Step 4: Click through each admin page**

Visit each URL and confirm proper rendering:
- `/admin/activitypub/` — stats, user table, configuration
- `/admin/staticpages/` — pages table, categories
- `/admin/styles/` — CSS textarea, file upload
- `/admin/standardsitesync/` — checkbox toggle
- `/admin/webhooks/` — dynamic webhook rows

- [ ] **Step 5: Click through each account page**

Visit each URL and confirm proper rendering:
- `/account/settings/standardsitesync/` — connection flow
- `/account/bridgy/` — Twitter connection card
- `/account/indiepub/` — token list, add form toggle

- [ ] **Step 6: Test with default theme**

Switch to the default theme at `/admin/themes/`. Visit `/admin/plugins/`, `/admin/staticpages/`, `/account/settings/` etc. Confirm all pages still render correctly using default templates. Switch back to Twenty26.

- [ ] **Step 7: Run test suite**

```bash
cd /Users/ben/code/idno && ./vendor/bin/phpunit --no-coverage
```

Expected: All existing tests pass (no regressions from additive-only template changes).

- [ ] **Step 8: Final commit if any cleanup needed**

If any fixes were needed during verification, commit them here.
