# Login, Logout & Search Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a modern login screen, logout nav link, and search modal to the Twenty26 theme.

**Architecture:** Three template overrides (login, search modal, vanilla JS forms/link) plus modifications to nav, shell, and navigation CSS. All interactivity uses Alpine.js inline — no new JS modules. All styling uses existing CSS classes.

**Tech Stack:** PHP (Bonita template engine), Alpine.js v3, Tailwind CSS 4, Lucide icons (inline SVG)

---

## Chunk 1: Login Screen + Forms/Link Override

### Task 1: Create the vanilla JS `forms/link.tpl.php` override

The default `forms/link.tpl.php` uses jQuery (`$('#id').submit()`) which doesn't work in Twenty26 (no jQuery loaded). This override replaces jQuery with `document.getElementById().submit()`. Needed before the logout link can work.

**Files:**
- Create: `Themes/Twenty26/templates/modern/forms/link.tpl.php`
- Reference: `templates/default/forms/link.tpl.php`

- [ ] **Step 1: Create the template file**

```php
<?php

    // Generate a unique ID for this form and link
    $uniqueID = uniqid('f');

    // Get HTTP method (GET, POST, PUT and DELETE supported for now)
    if (empty($vars['method']) || !in_array($vars['method'], array('GET','POST','PUT','DELETE'))) $vars['method'] = 'POST';

?>
<a data-form-id="<?= $uniqueID ?>" <?php if (!empty($vars['class'])) { ?> class="<?= $vars['class'] ?>" <?php
} ?> <?php if (!empty($vars['title'])) { ?> title="<?= $vars['title'] ?>" <?php
} ?> href="<?= $vars['url'] ?>" onclick="<?php
if ($vars['confirm']) {
    ?>if (confirm('<?= addslashes($vars['confirm-text']) ?>')) { document.getElementById('<?= $uniqueID ?>').submit(); return false; } else { return false; } <?php
} else {
    ?>document.getElementById('<?= $uniqueID ?>').submit(); return false; <?php
} ?>"><?= $vars['label'] ?></a>
<?php

    ob_start();

?>
<form action="<?= $vars['url'] ?>" style="display: none; margin: 0; padding: 0" id="<?= $uniqueID ?>" method="<?= $vars['method'] ?>">
    <textarea name="json"><?= htmlspecialchars(json_encode($vars['data'])) ?></textarea>
    <?= \Idno\Core\Idno::site()->actions()->signForm($vars['url']) ?>
</form>
<?php

    $form = ob_get_clean();
if (\Idno\Core\Idno::site()->currentPage()->xhr) {
    global $template_postponed_link_actions; // HORRIBLE HACK, to allow links to be active in xhr inserted controls. There *must* be a better way.

    if (empty($template_postponed_link_actions))
        $template_postponed_link_actions = "";

    $template_postponed_link_actions .= $form;
} else {
    \Idno\Core\Idno::site()->template()->extendTemplateWithContent('shell/form-data', $form);
}

    // Prevent scope pollution
    unset($this->vars['confirm-text']);
    unset($this->vars['class']);
    unset($this->vars['confirm']);
    unset($this->vars['url']);
    unset($this->vars['method']);
    unset($this->vars['data']);
    unset($this->vars['label']);
    unset($this->vars['id']);
```

Note: This is identical to the default template except:
1. `$('#<?php echo $uniqueID?>').submit()` → `document.getElementById('<?= $uniqueID ?>').submit()`
2. `<?php echo` → `<?=` (Twenty26 convention)

- [ ] **Step 2: Verify the file was created**

Run: `cat Themes/Twenty26/templates/modern/forms/link.tpl.php | head -5`
Expected: The opening PHP tag and unique ID generation.

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/forms/link.tpl.php
git commit -m "feat(twenty26): add vanilla JS forms/link override

Replaces jQuery \$('#id').submit() with document.getElementById().submit()
since Twenty26 does not load jQuery."
```

---

### Task 2: Create the login template

**Files:**
- Create: `Themes/Twenty26/templates/modern/account/login.tpl.php`
- Reference: `templates/default/account/login.tpl.php`
- Reference: `Idno/Pages/Session/Login.php` (handler passes `$vars['fwd']`)

- [ ] **Step 1: Create the template file**

```php
<div style="max-width:24rem;margin:2rem auto">
    <div class="idno-editor" style="text-align:center">
        <h4 class="idno-editor-heading">
            <?= \Idno\Core\Idno::site()->language()->_('Welcome back!') ?>
        </h4>

        <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>session/login" method="post">
            <div class="idno-form-field">
                <input type="text" name="email" autofocus
                       class="idno-input" style="text-align:left"
                       placeholder="<?= \Idno\Core\Idno::site()->language()->_('Your email address or username') ?>">
            </div>
            <div class="idno-form-field">
                <input type="password" name="password"
                       class="idno-input" style="text-align:left"
                       placeholder="<?= \Idno\Core\Idno::site()->language()->_('Password') ?>">
            </div>

            <?= $this->__(['action' => '/session/login'])->draw('forms/input/captcha') ?>

            <div class="idno-form-field">
                <button type="submit" class="idno-btn idno-btn-primary" style="width:100%">
                    <?= \Idno\Core\Idno::site()->language()->_('Sign in') ?>
                </button>
                <input type="hidden" name="fwd" value="<?php
                    if (!empty($vars['fwd'])) {
                        echo htmlspecialchars($vars['fwd']);
                    } else if (!empty($_SERVER['HTTP_REFERER'])) {
                        echo htmlspecialchars($_SERVER['HTTP_REFERER']);
                    } else {
                        echo \Idno\Core\Idno::site()->config()->getDisplayURL();
                    }
                ?>">
            </div>

            <?= \Idno\Core\Idno::site()->actions()->signForm('/session/login') ?>
        </form>

        <div style="margin-top:1rem;font-size:var(--font-size-sm);color:var(--color-text-muted)">
            <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/password" style="color:inherit">
                <?= \Idno\Core\Idno::site()->language()->_('Forgot your password?') ?>
            </a>
            <?php if (\Idno\Core\Idno::site()->config()->open_registration == true && \Idno\Core\Idno::site()->config()->canAddUsers()) { ?>
                <span style="margin:0 0.25rem">&middot;</span>
                <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/register" style="color:inherit">
                    <?= \Idno\Core\Idno::site()->language()->_('Register') ?>
                </a>
            <?php } ?>
        </div>
    </div>
</div>
```

Key differences from default:
- `idno-editor` card instead of Bootstrap `well`
- `idno-input` / `idno-btn-primary` classes instead of `form-control` / `btn-signin`
- `autofocus` attribute replaces jQuery `$('#inputEmail').focus()` script
- No jQuery script at the bottom
- Centered card with `max-width:24rem`

- [ ] **Step 2: Verify the template renders**

Run: Open `https://idno:8890/session/login` in browser while logged out.
Expected: Centered card with "Welcome back!" heading, email/password fields, sign in button, and forgot/register links.

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/account/login.tpl.php
git commit -m "feat(twenty26): add modern login template

Centered idno-editor card with modern form fields, replacing
Bootstrap 3 well and jQuery autofocus."
```

---

## Chunk 2: Logout Link, Search Modal, Nav & Shell Changes

### Task 3: Add button reset CSS for nav buttons

**Files:**
- Modify: `Themes/Twenty26/src/css/components/navigation.css`

- [ ] **Step 1: Add the button reset rule**

Add this rule after the existing `.idno-nav-item` block (after line 32, before the `:hover` rule), inside the `@layer components` block:

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

- [ ] **Step 2: Rebuild the CSS**

Run: `cd Themes/Twenty26 && npx @tailwindcss/cli -i src/css/main.css -o css/twenty26.css`
Expected: Build succeeds with no errors.

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/src/css/components/navigation.css Themes/Twenty26/css/twenty26.css
git commit -m "feat(twenty26): add button reset for nav items

Allows <button> elements with .idno-nav-item to render
identically to <a> nav items."
```

---

### Task 4: Create the search modal template

**Files:**
- Create: `Themes/Twenty26/templates/modern/shell/search.tpl.php`
- Reference: `Themes/Twenty26/templates/modern/shell/compose.tpl.php` (modal pattern)

- [ ] **Step 1: Create the template file**

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
            <button type="button" class="idno-modal-close" x-on:click="open = false">
                <?= $this->__(['icon' => 'x', 'class' => 'idno-nav-icon'])->draw('shell/icon') ?>
            </button>
        </div>
        <div class="idno-modal-body">
            <form x-on:submit.prevent="if (query.trim()) window.location.href = '/?q=' + encodeURIComponent(query.trim())"
                  style="display:flex;gap:0.5rem">
                <input type="search" x-model="query" x-ref="searchInput"
                       class="idno-input" style="flex:1"
                       aria-label="<?= \Idno\Core\Idno::site()->language()->_('Search') ?>"
                       placeholder="<?= \Idno\Core\Idno::site()->language()->_('Search posts...') ?>">
                <button type="submit" class="idno-btn idno-btn-primary">
                    <?= \Idno\Core\Idno::site()->language()->_('Search') ?>
                </button>
            </form>
        </div>
    </div>
</div>
```

Pattern notes:
- Uses `x-on:` long form to match compose modal convention
- Close button uses `shell/icon` draw call (same pattern as compose modal)
- Inline Alpine.js data — no JS module needed
- Auto-focuses input on open via `$nextTick`

- [ ] **Step 2: Verify the file was created**

Run: `cat Themes/Twenty26/templates/modern/shell/search.tpl.php | head -3`
Expected: The opening `<div x-data=` tag.

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/shell/search.tpl.php
git commit -m "feat(twenty26): add search modal template

Alpine.js modal overlay with search input. Listens for
open-search event, navigates to /?q=query on submit."
```

---

### Task 5: Update nav template (search button + logout link)

**Files:**
- Modify: `Themes/Twenty26/templates/modern/shell/nav.tpl.php`

Two changes:
1. Replace the Search `<a>` link with a `<button>` that dispatches `open-search`
2. Add a logout `<li>` as the last item in `<ul class="idno-nav-items">`, inside the logged-in block

- [ ] **Step 1: Replace the Search link with a button**

Change lines 25–30 (the Search `<li>`) from:

```php
        <li>
            <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>search/" class="idno-nav-item">
                <svg class="idno-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <span class="idno-nav-label"><?= \Idno\Core\Idno::site()->language()->_('Search') ?></span>
            </a>
        </li>
```

To:

```php
        <li>
            <button type="button" class="idno-nav-item" x-data x-on:click="$dispatch('open-search')">
                <svg class="idno-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <span class="idno-nav-label"><?= \Idno\Core\Idno::site()->language()->_('Search') ?></span>
            </button>
        </li>
```

Note: `x-data` is needed on the button so `$dispatch` works (Alpine requires an Alpine-managed element).

- [ ] **Step 2: Add the logout nav item**

Add a new `<li>` as the last item inside `<ul class="idno-nav-items">`, before the closing `</ul>` on line 47. It must be inside a `<?php if (!empty($user)) { ?>` block. The cleanest placement is right before the `</ul>`:

Replace lines 46–47:

```php
        <?php } ?>
    </ul>
```

With:

```php
        <?php } ?>
        <?php if (!empty($user)) { ?>
        <li>
            <?= \Idno\Core\Idno::site()->actions()->createLink(
                \Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/logout',
                '<svg class="idno-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg><span class="idno-nav-label">'
                    . \Idno\Core\Idno::site()->language()->_('Log out')
                    . '</span>',
                [],
                ['class' => 'idno-nav-item']
            ) ?>
        </li>
        <?php } ?>
    </ul>
```

The SVG is the Lucide "log-out" icon. `createLink()` generates a hidden form with CSRF token and an `<a>` with onclick that submits it — our `forms/link.tpl.php` override ensures it uses vanilla JS.

- [ ] **Step 3: Verify the nav renders**

Run: Open `https://idno:8890/` in browser while logged in.
Expected: Nav shows Home, Profile, Search (now a button), Drafts, Settings (if admin), Log out, then the New Post button.

- [ ] **Step 4: Commit**

```bash
git add Themes/Twenty26/templates/modern/shell/nav.tpl.php
git commit -m "feat(twenty26): add search button and logout link to nav

Search nav item is now a button that dispatches open-search event.
Log out link added below Settings using createLink() for CSRF."
```

---

### Task 6: Add search modal draw call to shell template

**Files:**
- Modify: `Themes/Twenty26/templates/modern/shell.tpl.php`

- [ ] **Step 1: Add the search draw call**

Add `<?= $template->draw('shell/search') ?>` immediately before the existing `<?= $template->draw('shell/compose') ?>` line (line 48).

Change line 48 from:

```php
    <?= $template->draw('shell/compose') ?>
```

To:

```php
    <?= $template->draw('shell/search') ?>
    <?= $template->draw('shell/compose') ?>
```

- [ ] **Step 2: Commit**

```bash
git add Themes/Twenty26/templates/modern/shell.tpl.php
git commit -m "feat(twenty26): draw search modal in shell

Adds shell/search template draw call before shell/compose."
```

---

### Task 7: Full integration test

- [ ] **Step 1: Rebuild CSS**

Run: `cd Themes/Twenty26 && npx @tailwindcss/cli -i src/css/main.css -o css/twenty26.css`
Expected: Build succeeds.

- [ ] **Step 2: Test login screen**

Open `https://idno:8890/session/login` while logged out.
Expected:
- Centered card with "Welcome back!" heading
- Email and password fields with placeholders
- Sign in button (full width)
- Forgot password link, and Register link if registration is open
- No console errors

- [ ] **Step 3: Test login form submission**

Enter credentials and click Sign in.
Expected: Redirects to homepage (or fwd URL) on success, shows error message on failure.

- [ ] **Step 4: Test search modal**

Click "Search" in the sidebar nav.
Expected:
- Modal overlay appears with dimmed background
- Search input is auto-focused
- Type a query and press Enter → navigates to `/?q=query`
- Press Escape → modal closes
- Click outside modal → modal closes

- [ ] **Step 5: Test logout**

Click "Log out" in the sidebar nav.
Expected: User is logged out and redirected (typically to homepage or login).

- [ ] **Step 6: Commit CSS rebuild if needed**

```bash
git add Themes/Twenty26/css/twenty26.css
git commit -m "build(twenty26): rebuild CSS with nav button reset"
```
