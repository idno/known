# Modern Template Overrides Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace Bootstrap 3 fallback templates on user-facing pages with modern Tailwind CSS 4 overrides so the Twenty26 theme renders correctly.

**Architecture:** New template files in `Themes/Twenty26/templates/modern/` override the `default/` Bootstrap templates via Bonita's template type resolution. A new `annotations.css` component file provides all annotation/comment/pagination styles using `@layer components`. No default templates are modified.

**Tech Stack:** PHP templates (Bonita engine), Tailwind CSS 4, Vite, PostCSS

**Spec:** `docs/superpowers/specs/2026-03-12-modern-template-overrides-design.md`

---

## File Structure

**New files:**
```
Themes/Twenty26/src/css/components/annotations.css    — All annotation, comment, pagination, content-type-bar, syndication styles
Themes/Twenty26/templates/modern/pages/home.tpl.php   — Homepage (filter bar + feed)
Themes/Twenty26/templates/modern/content/create.tpl.php — Content type filter bar
Themes/Twenty26/templates/modern/entity/feed.tpl.php  — Feed iterator + pagination
Themes/Twenty26/templates/modern/shell/pagination.tpl.php — Newer/Older pagination
Themes/Twenty26/templates/modern/content/edit.tpl.php — Edit/Delete post links
Themes/Twenty26/templates/modern/content/end/annotations.tpl.php — Annotation orchestrator
Themes/Twenty26/templates/modern/content/annotation/edit.tpl.php — Annotation delete control
Themes/Twenty26/templates/modern/entity/annotations/comment/main.tpl.php — Comment form
Themes/Twenty26/templates/modern/entity/annotations/likes.tpl.php — Like list
Themes/Twenty26/templates/modern/entity/annotations/shares.tpl.php — Share list
Themes/Twenty26/templates/modern/entity/annotations/mentions.tpl.php — Mention list
Themes/Twenty26/templates/modern/entity/annotations/rsvps.tpl.php — RSVP list
Themes/Twenty26/templates/modern/content/syndication/links.tpl.php — POSSE links
```

**Modified files:**
```
Themes/Twenty26/src/css/main.css — Add annotations.css import
Themes/Twenty26/templates/modern/entity/annotations/replies.tpl.php — Consistency update to use idno-annotation classes
```

---

## Chunk 1: CSS Foundation + Homepage Templates

### Task 1: Create annotations.css and update main.css

**Files:**
- Create: `Themes/Twenty26/src/css/components/annotations.css`
- Modify: `Themes/Twenty26/src/css/main.css`

- [ ] **Step 1: Create `annotations.css`**

```css
@layer components {
  /* Annotation rows (likes, shares, mentions, rsvps) */
  .idno-annotation {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-gap);
    padding: 0.5rem 0;
  }

  .idno-annotation + .idno-annotation {
    border-top: 1px solid var(--color-border-subtle);
  }

  .idno-annotation-avatar {
    width: 1.5rem;
    height: 1.5rem;
    border-radius: var(--radius-full);
    object-fit: cover;
    flex-shrink: 0;
  }

  .idno-annotation-content {
    flex: 1;
    min-width: 0;
    font-size: var(--font-size-sm);
    color: var(--color-text);
    line-height: 1.4;
  }

  .idno-annotation-content a {
    color: var(--color-text-strong);
    text-decoration: none;
  }

  .idno-annotation-content a:hover {
    text-decoration: underline;
  }

  .idno-annotation-section {
    padding-top: var(--spacing-section);
    margin-bottom: 0.25rem;
  }

  .idno-annotation-section h4 {
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--color-text-strong);
    margin: 0;
  }

  .idno-annotation-delete {
    font-size: var(--font-size-sm);
    flex-shrink: 0;
  }

  .idno-annotation-delete a {
    color: var(--color-text-muted);
    text-decoration: none;
    font-size: var(--font-size-sm);
  }

  .idno-annotation-delete a:hover {
    color: #dc2626;
  }

  /* Annotations list container (class set by content/end.tpl.php) */
  .idno-annotations {
    margin-top: var(--spacing-section);
    padding-top: var(--spacing-section);
    border-top: 1px solid var(--color-border-subtle);
  }

  /* Comment form */
  .idno-comment-form {
    display: flex;
    gap: var(--spacing-gap);
    margin-top: var(--spacing-section);
    padding-top: var(--spacing-section);
    border-top: 1px solid var(--color-border-subtle);
  }

  .idno-comment-form form {
    flex: 1;
    min-width: 0;
  }

  .idno-comment-form .idno-textarea {
    min-height: 4rem;
  }

  .idno-comment-form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.5rem;
  }

  /* Content type filter bar */
  .idno-content-type-bar {
    display: flex;
    gap: 0.25rem;
    margin-bottom: var(--spacing-section);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .idno-content-type-tab {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--color-text-secondary);
    text-decoration: none;
    white-space: nowrap;
    transition: background 0.15s, color 0.15s;
  }

  .idno-content-type-tab:hover {
    background: rgba(0, 0, 0, 0.04);
    color: var(--color-text-strong);
  }

  .idno-content-type-tab.active {
    background: var(--color-primary);
    color: var(--color-primary-text);
  }

  /* Syndication links */
  .idno-syndication-links {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: var(--font-size-sm);
  }

  .idno-syndication-links a {
    color: var(--color-text-muted);
    text-decoration: none;
  }

  .idno-syndication-links a:hover {
    color: var(--color-text-strong);
    text-decoration: underline;
  }

  /* Pagination */
  .idno-pagination {
    margin-top: var(--spacing-section);
    padding-top: var(--spacing-section);
  }

  .idno-pagination ul {
    display: flex;
    justify-content: space-between;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .idno-pagination li a {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--color-text-secondary);
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
  }

  .idno-pagination li a:hover {
    background: rgba(0, 0, 0, 0.04);
    color: var(--color-text-strong);
  }

  .idno-pagination li.pagination-disabled a {
    color: var(--color-text-muted);
    opacity: 0.4;
    pointer-events: none;
  }
}
```

- [ ] **Step 2: Add import to `main.css`**

In `Themes/Twenty26/src/css/main.css`, add the import after the existing component imports:

```css
@import "./components/annotations.css";
```

The full file should be:
```css
@import "tailwindcss";
@import "./tokens.css";
@import "./components/layout.css";
@import "./components/navigation.css";
@import "./components/post-card.css";
@import "./components/buttons.css";
@import "./components/forms.css";
@import "./components/modal.css";
@import "./components/editor.css";
@import "./components/profile.css";
@import "./components/admin.css";
@import "./components/annotations.css";
@source "../../templates/";
@source "../../../../IdnoPlugins/*/templates/modern/";
```

- [ ] **Step 3: Commit CSS foundation**

```bash
git add Themes/Twenty26/src/css/components/annotations.css Themes/Twenty26/src/css/main.css
git commit -m "feat: add annotations.css component with all annotation, comment, pagination styles"
```

### Task 2: Create homepage and feed templates

**Files:**
- Create: `Themes/Twenty26/templates/modern/pages/home.tpl.php`
- Create: `Themes/Twenty26/templates/modern/content/create.tpl.php`
- Create: `Themes/Twenty26/templates/modern/entity/feed.tpl.php`
- Create: `Themes/Twenty26/templates/modern/shell/pagination.tpl.php`

- [ ] **Step 1: Create `pages/home.tpl.php`**

```php
<?php

if (!empty($vars['contentTypes'])) {

    if (\Idno\Core\Idno::site()->canWrite()) {
        echo $this->draw('content/create');
    }

} else {

    echo $this->draw('pages/home/blurb');

}

    echo $this->draw('entity/feed');
```

This mirrors the default but drops the `robot/wizard` draw (legacy onboarding not used in modern theme).

- [ ] **Step 2: Create `content/create.tpl.php`**

```php
<?php

if (!empty($vars['contentTypes']) && is_array($vars['contentTypes'])) {

?>
<div class="idno-content-type-bar">
    <?php
    foreach ($vars['contentTypes'] as $contentType) {
        /* @var \Idno\Common\ContentType $contentType */
    ?>
        <a class="idno-content-type-tab"
           href="<?= $contentType->getEditURL() ?>">
            <span class="idno-content-type-icon"><?= $contentType->getIcon() ?></span>
            <?= $contentType->getTitle() ?>
        </a>
    <?php
    }
    ?>
</div>
<?php

}

?>
```

- [ ] **Step 3: Create `entity/feed.tpl.php`**

```php
<?php

if (!empty($vars['items'])) {

    foreach ($vars['items'] as $entry) {
        if ($entry instanceof \Idno\Common\Entity) {
            echo $this->__(['object' => $entry])->draw('entity/shell');
        }
    }

    if (!empty($vars['count'])) {
        echo $this->drawPagination($vars['count']);
    }

} else {
    echo $this->draw('pages/home/nocontent');
}
```

- [ ] **Step 4: Create `shell/pagination.tpl.php`**

```php
<?php

    /* @var $this \Idno\Core\Template */
    $xhr = false;

if (isset($vars['offset']) && !empty($vars['count'])) {

    if (empty($vars['items_per_page'])) {
        $items_per_page = \Idno\Core\Idno::site()->config()->items_per_page;
    } else {
        $items_per_page = $vars['items_per_page'];
    }
    $prev_offset = $vars['offset'] - $items_per_page;
    if ($prev_offset < 0) $prev_offset = 0;
    $next_offset = $vars['offset'] + $items_per_page;
    if ($next_offset > ($vars['count'] - 1)) $next_offset = $vars['count'] - 1;
    ?>

        <nav class="idno-pagination <?php

        if (!empty($vars['control-id']) && (!empty($vars['source-url']))) {
            echo "pager-xhr";
            $xhr = true;
        }

        ?>" data-count="<?= $vars['count'] ?>" data-limit="<?= $items_per_page ?>" data-offset="<?= $vars['offset'] ?>" data-control-id="<?= empty($vars['control-id']) ? '' : $vars['control-id'] ?>" data-source-url="<?= empty($vars['source-url']) ? '' : htmlspecialchars($vars['source-url']) ?>">
            <ul>
                <li class="newer <?php if ($vars['offset'] == 0) echo "pagination-disabled" ?>">
                    <a href="<?= !$xhr ? $this->getURLWithVar('offset', $prev_offset) : '#' ?>" rel="next">
                        <span>&laquo; <?= \Idno\Core\Idno::site()->language()->_('Newer') ?></span>
                    </a>
                </li>
                <li class="older <?php if ($vars['offset'] > $vars['count'] - $items_per_page) echo "pagination-disabled" ?>">
                    <a href="<?= !$xhr ? $this->getURLWithVar('offset', $next_offset) : '#' ?>" rel="prev">
                        <span><?= \Idno\Core\Idno::site()->language()->_('Older') ?> &raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>

    <?php

}
```

- [ ] **Step 5: Commit homepage and feed templates**

```bash
git add Themes/Twenty26/templates/modern/pages/home.tpl.php \
  Themes/Twenty26/templates/modern/content/create.tpl.php \
  Themes/Twenty26/templates/modern/entity/feed.tpl.php \
  Themes/Twenty26/templates/modern/shell/pagination.tpl.php
git commit -m "feat: add modern homepage, feed, content-type-bar, and pagination templates"
```

### Task 3: Create edit/delete and syndication link templates

**Files:**
- Create: `Themes/Twenty26/templates/modern/content/edit.tpl.php`
- Create: `Themes/Twenty26/templates/modern/content/syndication/links.tpl.php`

- [ ] **Step 1: Create `content/edit.tpl.php`**

```php
<?php /* @var \Idno\Common\Entity $vars['object'] */ ?>
<?php

if ($vars['object']->canEdit()) {

    ?>
        <a href="<?= $vars['object']->getEditURL() ?>" class="idno-entry-action"><?= \Idno\Core\Idno::site()->language()->_('Edit') ?></a>
    <?= \Idno\Core\Idno::site()->actions()->createLink(
        $vars['object']->getDeleteURL(),
        \Idno\Core\Idno::site()->language()->_('Delete'),
        [],
        [
            'method' => 'POST',
            'class' => 'idno-entry-action',
            'confirm' => true,
            'confirm-text' => \Idno\Core\Idno::site()->language()->_('Are you sure you want to permanently delete this entry?')
        ]
    ) ?>
    <?= $this->draw('content/entity/' . $vars['object']->getEntityTypeName() . '/edit') ?>
    <?php

}
```

- [ ] **Step 2: Create `content/syndication/links.tpl.php`**

```php
<?php

if ($posse = $vars['object']->getPosseLinks()) {

    ?>
<div class="idno-syndication-links">
    <span class="idno-entry-meta"><?= \Idno\Core\Idno::site()->language()->_('Also on:') ?></span>
    <?php

    foreach ($posse as $service => $posse_links) {
        if (is_string($posse_links)) {
            $posse_links = [['url' => $posse_links, 'identifier' => $service]];
        }

        foreach ($posse_links as $element) {
            $human_icon = $this->__(
                [
                    'username' => isset($element['account_id']) ? $element['account_id'] : false,
                    'details'  => $element,
                ]
            )->draw('content/syndication/icon/' . $service);

            if (empty($human_icon)) $human_icon = $this->draw('content/syndication/icon/generic');
            if (empty($element['url'])) $element['url'] = '#';
            if (empty($element['identifier'])) $element['identifier'] = '';

            $rel_syndication = '';
            if (\Idno\Core\Idno::site()->currentPage()->isPermalink()) {
                $rel_syndication = ' rel="syndication"';
            }

            echo "<a href=\"{$element['url']}\"$rel_syndication class=\"u-syndication\">$human_icon {$element['identifier']}</a>";
        }
    }

    ?>
</div>
    <?php

}
```

- [ ] **Step 3: Commit edit and syndication templates**

```bash
git add Themes/Twenty26/templates/modern/content/edit.tpl.php \
  Themes/Twenty26/templates/modern/content/syndication/links.tpl.php
git commit -m "feat: add modern edit/delete and syndication link templates"
```

---

## Chunk 2: Annotation Templates + Build

### Task 4: Create annotation orchestrator and delete control

**Files:**
- Create: `Themes/Twenty26/templates/modern/content/end/annotations.tpl.php`
- Create: `Themes/Twenty26/templates/modern/content/annotation/edit.tpl.php`

- [ ] **Step 1: Create `content/end/annotations.tpl.php`**

Note: The parent template `content/end.tpl.php` already wraps this in `<div class="idno-annotations">`, so this template does NOT add its own wrapper to avoid double-nesting.

```php
<?php

foreach ([
    'rsvp'    => 'rsvps',
    'like'    => 'likes',
    'share'   => 'shares',
    'reply'   => 'replies',
    'mention' => 'mentions'] as $annotationType => $templateName) {

    if ($annotations = $vars['object']->getAnnotations($annotationType)) {
        echo $this->__(array('annotations' => $annotations))->draw('entity/annotations/' . $templateName);
    }

}
```

- [ ] **Step 2: Create `content/annotation/edit.tpl.php`**

```php
<span class="idno-annotation-delete">
    <?= \Idno\Core\Idno::site()->actions()->createLink(
        $vars['object']->getDisplayUrl() . '/annotation/delete?permalink=' . \Idno\Core\Webservice::base64UrlEncode($vars['annotation_permalink']),
        \Idno\Core\Idno::site()->language()->_('Delete'),
        [],
        ['method' => 'POST']
    ) ?>
</span>
```

- [ ] **Step 3: Commit orchestrator and delete control**

```bash
git add Themes/Twenty26/templates/modern/content/end/annotations.tpl.php \
  Themes/Twenty26/templates/modern/content/annotation/edit.tpl.php
git commit -m "feat: add modern annotation orchestrator and delete control templates"
```

### Task 5: Create comment form template

**Files:**
- Create: `Themes/Twenty26/templates/modern/entity/annotations/comment/main.tpl.php`

- [ ] **Step 1: Create `entity/annotations/comment/main.tpl.php`**

```php
<?php

    $user = \Idno\Core\Idno::site()->session()->currentUser();
    $object = $vars['object'];

if (!empty($user) && !empty($object)) {

?>
    <div class="idno-comment-form">
        <a href="<?= $user->getDisplayURL() ?>" class="u-url">
            <img class="idno-annotation-avatar u-photo"
                 src="<?= $user->getIcon() ?>"
                 alt="<?= htmlspecialchars($user->getTitle()) ?>" />
        </a>
        <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>annotation/post" method="post">
            <textarea name="body"
                      placeholder="<?= \Idno\Core\Idno::site()->language()->_('Add a comment ...') ?>"
                      class="idno-textarea mentionable ctrl-enter-submit"></textarea>
            <?= \Idno\Core\Idno::site()->actions()->signForm('annotation/post') ?>
            <input type="hidden" name="object" value="<?= $object->getUUID() ?>">
            <input type="hidden" name="type" value="reply">
            <div class="idno-comment-form-actions">
                <button type="submit" class="idno-btn idno-btn-primary idno-btn-sm">
                    <?= \Idno\Core\Idno::site()->language()->_('Comment') ?>
                </button>
            </div>
        </form>
    </div>
<?php

    unset($this->vars['action']);
}
```

- [ ] **Step 2: Commit comment form**

```bash
git add Themes/Twenty26/templates/modern/entity/annotations/comment/main.tpl.php
git commit -m "feat: add modern comment form template"
```

### Task 6: Create likes, shares, mentions, and rsvps templates

**Files:**
- Create: `Themes/Twenty26/templates/modern/entity/annotations/likes.tpl.php`
- Create: `Themes/Twenty26/templates/modern/entity/annotations/shares.tpl.php`
- Create: `Themes/Twenty26/templates/modern/entity/annotations/mentions.tpl.php`
- Create: `Themes/Twenty26/templates/modern/entity/annotations/rsvps.tpl.php`

- [ ] **Step 1: Create `entity/annotations/likes.tpl.php`**

```php
<?php

if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    uasort(
        $vars['annotations'], function ($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        }
    );
    foreach ($vars['annotations'] as $locallink => $annotation) {
        $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
?>
        <div class="idno-annotation">
            <?php if (!empty($annotation['owner_image'])) { ?>
            <img class="idno-annotation-avatar"
                 src="<?= htmlspecialchars($annotation['owner_image']) ?>"
                 alt="<?= htmlspecialchars($annotation['owner_name']) ?>" />
            <?php } ?>
            <div class="idno-annotation-content">
                <span>
                    <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" rel="nofollow">
                        <?= htmlentities($annotation['owner_name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <?= \Idno\Core\Idno::site()->language()->_('liked this post') ?>
                </span>
                <br>
                <span class="idno-entry-meta">
                    <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= date('M j, Y', $annotation['time']) ?></a>
                    on <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= parse_url($permalink, PHP_URL_HOST) ?></a>
                </span>
            </div>
            <?php
            $this->annotation_permalink = $locallink;
            if ($vars['object']->canEditAnnotation($annotation)) {
                echo $this->draw('content/annotation/edit');
            }
            ?>
        </div>
<?php
    }
}
```

- [ ] **Step 2: Create `entity/annotations/shares.tpl.php`**

```php
<?php

if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    uasort(
        $vars['annotations'], function ($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        }
    );
    foreach ($vars['annotations'] as $locallink => $annotation) {
        $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
?>
        <div class="idno-annotation">
            <?php if (!empty($annotation['owner_image'])) { ?>
            <img class="idno-annotation-avatar"
                 src="<?= htmlspecialchars($annotation['owner_image']) ?>"
                 alt="<?= htmlspecialchars($annotation['owner_name']) ?>" />
            <?php } ?>
            <div class="idno-annotation-content">
                <span>
                    <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" rel="nofollow">
                        <?= htmlentities($annotation['owner_name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow">
                        <?= \Idno\Core\Idno::site()->language()->_('reshared this post') ?>
                    </a>
                </span>
                <br>
                <span class="idno-entry-meta">
                    <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= date('M j, Y', $annotation['time']) ?></a>
                    on <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= parse_url($permalink, PHP_URL_HOST) ?></a>
                </span>
            </div>
            <?php
            $this->annotation_permalink = $locallink;
            if ($vars['object']->canEditAnnotation($annotation)) {
                echo $this->draw('content/annotation/edit');
            }
            ?>
        </div>
<?php
    }
}
```

- [ ] **Step 3: Create `entity/annotations/mentions.tpl.php`**

```php
<?php

if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    uasort(
        $vars['annotations'], function ($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        }
    );
    foreach ($vars['annotations'] as $locallink => $annotation) {
        $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
?>
        <div class="idno-annotation">
            <?php if (!empty($annotation['owner_image'])) { ?>
            <img class="idno-annotation-avatar"
                 src="<?= htmlspecialchars($annotation['owner_image']) ?>"
                 alt="<?= htmlspecialchars($annotation['owner_name']) ?>" />
            <?php } ?>
            <div class="idno-annotation-content">
                <span>
                    <?= \Idno\Core\Idno::site()->language()->_('Mentioned in') ?>:
                    <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?php
                        if (!empty($annotation['title'])) {
                            echo htmlspecialchars($annotation['title']);
                        } else {
                            echo htmlspecialchars($permalink);
                        }
                    ?></a>
                </span>
                <br>
                <span class="idno-entry-meta">
                    <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" rel="nofollow">
                        <?= htmlentities($annotation['owner_name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>,
                    <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= date('M j, Y', $annotation['time']) ?></a>
                    on <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= parse_url($permalink, PHP_URL_HOST) ?></a>
                </span>
            </div>
            <?php
            $this->annotation_permalink = $locallink;
            if ($vars['object']->canEditAnnotation($annotation)) {
                echo $this->draw('content/annotation/edit');
            }
            ?>
        </div>
<?php
    }
}
```

- [ ] **Step 4: Create `entity/annotations/rsvps.tpl.php`**

```php
<?php

if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    usort(
        $vars['annotations'], function ($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        }
    );

    $rsvps_by_response = ['yes' => '', 'maybe' => '', 'no' => '', 'etc' => ''];

    foreach ($vars['annotations'] as $locallink => $annotation) {
        $permalink = !empty($annotation['permalink']) ? $annotation['permalink'] : $locallink;
        $rsvp = !empty($annotation['rsvp']) ? strtolower(trim($annotation['rsvp'])) : 'etc';

        ob_start();
?>
            <div class="idno-annotation">
                <?php if (!empty($annotation['owner_image'])) { ?>
                <img class="idno-annotation-avatar"
                     src="<?= htmlspecialchars($annotation['owner_image']) ?>"
                     alt="<?= htmlspecialchars($annotation['owner_name']) ?>" />
                <?php } ?>
                <div class="idno-annotation-content">
                    <span><strong><?= strip_tags($annotation['content']) ?></strong></span>
                    <br>
                    <span class="idno-entry-meta">
                        <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= date('M j, Y', $annotation['time']) ?></a>
                        on <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= parse_url($permalink, PHP_URL_HOST) ?></a>
                    </span>
                </div>
                <?php
                $this->annotation_permalink = $locallink;
                if ($vars['object']->canEditAnnotation($annotation)) {
                    echo $this->draw('content/annotation/edit');
                }
                ?>
            </div>
<?php
        $rsvps_by_response[$rsvp] .= ob_get_clean();
    }

    foreach ($rsvps_by_response as $rsvp => $list) {
        if (!empty($list)) {
            switch ($rsvp) {
                case 'yes':
                    $title = \Idno\Core\Idno::site()->language()->_('Attending');
                    break;
                case 'maybe':
                    $title = \Idno\Core\Idno::site()->language()->_('Maybe attending');
                    break;
                case 'no':
                    $title = \Idno\Core\Idno::site()->language()->_('Not attending');
                    break;
                case 'etc':
                    $title = \Idno\Core\Idno::site()->language()->_('Other responses');
                    break;
            }
?>
            <div class="idno-annotation-section">
                <h4><?= $title ?></h4>
            </div>
<?php
            echo $list;
        }
    }
}
```

- [ ] **Step 5: Commit annotation templates**

```bash
git add Themes/Twenty26/templates/modern/entity/annotations/likes.tpl.php \
  Themes/Twenty26/templates/modern/entity/annotations/shares.tpl.php \
  Themes/Twenty26/templates/modern/entity/annotations/mentions.tpl.php \
  Themes/Twenty26/templates/modern/entity/annotations/rsvps.tpl.php
git commit -m "feat: add modern likes, shares, mentions, and rsvps annotation templates"
```

### Task 7: Update replies.tpl.php for consistency

**Files:**
- Modify: `Themes/Twenty26/templates/modern/entity/annotations/replies.tpl.php`

- [ ] **Step 1: Update `replies.tpl.php` to use `idno-annotation` classes**

Replace the entire file content with:

```php
<?php
if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    uasort(
        $vars['annotations'], function ($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        }
    );
    foreach ($vars['annotations'] as $locallink => $annotation) {
        $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
?>
    <div class="idno-annotation h-cite">
        <?php if (!empty($annotation['owner_image'])) { ?>
        <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" class="u-url">
            <img class="idno-annotation-avatar u-photo"
                 src="<?= htmlspecialchars($annotation['owner_image']) ?>"
                 alt="<?= htmlspecialchars($annotation['owner_name']) ?>" />
        </a>
        <?php } ?>
        <div class="idno-annotation-content">
            <div>
                <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" class="p-name u-url p-author h-card" rel="nofollow" style="font-weight:600;color:var(--color-text-strong)">
                    <?= htmlspecialchars($annotation['owner_name']) ?>
                </a>
                <span class="idno-entry-meta">
                    <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow" class="u-url">
                        <time class="dt-published" datetime="<?= date(DATE_ATOM, $annotation['time']) ?>">
                            <?= date('M j, Y', $annotation['time']) ?>
                        </time>
                    </a>
                </span>
            </div>
            <?php if (!empty($annotation['content'])) { ?>
            <div class="e-content" style="margin-top:0.25rem">
                <?= $this->autop($this->parseURLs(strip_tags($annotation['content']), 'rel="nofollow"')) ?>
            </div>
            <?php } ?>
            <?php if (!empty($permalink)) { ?>
            <a href="<?= htmlspecialchars($permalink) ?>" class="u-url idno-entry-meta" rel="nofollow">
                <?= parse_url($permalink, PHP_URL_HOST) ?>
            </a>
            <?php } ?>
        </div>
        <?php
            $this->annotation_permalink = $locallink;
            if ($vars['object']->canEditAnnotation($annotation)) {
                echo $this->draw('content/annotation/edit');
            }
        ?>
    </div>
<?php
    }
}
?>
```

- [ ] **Step 2: Commit replies update**

```bash
git add Themes/Twenty26/templates/modern/entity/annotations/replies.tpl.php
git commit -m "refactor: update replies template to use idno-annotation classes for consistency"
```

### Task 8: Build and verify

- [ ] **Step 1: Run the Vite build**

```bash
cd Themes/Twenty26 && npm run build
```

Expected: Build succeeds, `dist/modern.min.css` and `dist/modern.min.js` are regenerated with the new annotation styles included.

- [ ] **Step 2: Verify in browser — Homepage**

Navigate to `https://idno:8890/`. Check:
- Content type filter bar displays as horizontal pills at top of feed
- Feed posts render correctly with no layout issues
- Pagination at bottom of feed is styled (Newer/Older links)
- No unstyled Bootstrap class elements visible

- [ ] **Step 3: Verify in browser — Profile page**

Navigate to `https://idno:8890/profile/ben`. Check:
- Profile card renders cleanly (no overlapping elements)
- Posts on profile page have styled edit/delete links
- No overlapping comment forms or avatars

- [ ] **Step 4: Verify in browser — Single post**

Click any post to view its permalink page. Check:
- Comment form appears below post with avatar and textarea side by side
- Edit/Delete links are styled as action links
- Syndication links (if any) display as subtle metadata

- [ ] **Step 5: Commit built assets**

```bash
git add Themes/Twenty26/dist/
git commit -m "chore: rebuild dist with modern template CSS"
```

- [ ] **Step 6: Visual polish pass**

Review each page for spacing, alignment, and visual consistency issues. Fix any CSS that needs adjustment in `annotations.css`. If changes are made:

```bash
cd Themes/Twenty26 && npm run build
git add Themes/Twenty26/src/css/components/annotations.css Themes/Twenty26/dist/
git commit -m "fix: adjust annotation CSS spacing and alignment"
```
