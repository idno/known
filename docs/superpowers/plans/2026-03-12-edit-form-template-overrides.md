# Edit Form Template Overrides Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create 5 modern template overrides that replace Bootstrap-dependent shared form infrastructure with `idno-*` CSS classes and inline SVG icons, so edit forms render correctly in the Twenty26 theme.

**Architecture:** Override templates in `Themes/Twenty26/templates/modern/` — Bonita checks `templates/modern/` first, falls back to `templates/default/`. Each override copies the PHP logic from the default, swaps CSS classes, and replaces Font Awesome icons with inline SVGs. One new CSS component (`.idno-access-dropdown`) added to `forms.css`.

**Tech Stack:** PHP (Bonita template engine), Alpine.js (access dropdown), CSS (`@layer components`), inline SVGs (Lucide-style)

**Spec:** `docs/superpowers/specs/2026-03-12-edit-form-template-overrides-design.md`

---

## Chunk 1: Simple Form Primitives

These three templates are straightforward class swaps with no new logic.

### Task 1: Create `forms/input/input.tpl.php` override

**Files:**
- Read: `templates/default/forms/input/input.tpl.php` (source to copy from)
- Create: `Themes/Twenty26/templates/modern/forms/input/input.tpl.php`

- [ ] **Step 1: Create the override file**

Create `Themes/Twenty26/templates/modern/forms/input/input.tpl.php` with the exact same PHP logic as the default. The only change is on the `class=` line — replace `form-control input` with `idno-input`:

```php
<?php
// Define possible fields and their defaults, a boolean FALSE means don't show if not present
$fields_and_defaults = array(
    'type' => 'text',
    'name' => false,
    'id' => false,
    'autocomplete' => false,
    'autofocus' => false,
    'accept' => false,
    'checked' => false,
    'disabled' => false,
    'min' => false,
    'max' => false,
    'step' => false,
    'maxlength' => false,
    'multiple' => false,
    'pattern' => false,
    'readonly' => false,
    'required' => false,
    'src' => false,
    'spellcheck' => false,
    //'placeholder' => false,
    'accept' => false,
    'onclick' => false,
    'onfocus' => false,
    'onblur' => false,
    'onchange' => false,
);

// We always want a unique ID
global $input_id;
if (!isset($vars['id'])) {
    $input_id ++;
    $vars['id'] = $vars['name'] . "_$input_id";
}
?>
<input
<?php

$published = [];
if (isset($vars['placeholder']))
    $published['placeholder'] = $vars['placeholder'];
if (isset($vars['alt']))
    $published['alt'] = $vars['alt'];

foreach ($fields_and_defaults as $field => $default) {
    if (isset($vars[$field])) {
        if ($vars[$field] === true) {
            echo "$field ";
            $published[$field] = true;
        } else {
            echo "$field=\"{$vars[$field]}\" ";
            $published[$field] = $vars[$field];
        }
    }
    else {
        if ($default !== false) {
            if ($default === true) {
                echo "$field ";
                $published[$field] = true;
            } else {
                echo "$field=\"$default\" ";
                $published[$field] = $default;
            }
        }
    }
}
?>
    class="idno-input <?php echo isset($vars['class']) ? $vars['class'] : 'input-' . (isset($vars['type']) ? $vars['type'] : 'text'); ?>"
<?php if (isset($vars['placeholder'])) { ?>placeholder="<?php echo htmlentities($vars['placeholder'], ENT_QUOTES, 'UTF-8'); ?>" <?php
} // Placeholder is a special case ?>
<?php if (isset($vars['alt'])) { ?>alt="<?php echo htmlentities($vars['alt'], ENT_QUOTES, 'UTF-8'); ?>" <?php
} // Alt is a special case ?>
    value="<?php if (isset($vars['value'])) echo htmlentities($vars['value'], ENT_QUOTES, 'UTF-8'); ?>"
    />
<?php
// Ensure this is documented in the api get
if (!empty($published['alt'])) {
    $published['description'] = $published['alt'];
    unset($published['alt']); unset($published['placeholder']);
} else if (!empty($published['placeholder'])) {
    $published['description'] = $published['placeholder'];
    unset($published['alt']); unset($published['placeholder']);
}

// Document form
$this->documentFormControl($vars['name'], $published);

// Prevent bonita polution
foreach (array_merge($fields_and_defaults, ['placeholder' => false, 'value' => '', 'class' => '']) as $field => $default)
    unset($this->vars[$field]);
```

- [ ] **Step 2: Verify the change**

Diff the new file against `templates/default/forms/input/input.tpl.php`. The ONLY difference should be:
- Line with `class=`: `form-control input` → `idno-input`
- Trailing whitespace after `/>` removed (the default has a stray space)

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/forms/input/input.tpl.php
git commit -m "feat: add modern input.tpl.php override (form-control → idno-input)"
```

---

### Task 2: Create `forms/input/longtext.tpl.php` override

**Files:**
- Read: `templates/default/forms/input/longtext.tpl.php` (source to copy from)
- Create: `Themes/Twenty26/templates/modern/forms/input/longtext.tpl.php`

- [ ] **Step 1: Create the override file**

Create `Themes/Twenty26/templates/modern/forms/input/longtext.tpl.php`. Changes from default:
1. Drop the `<br class="clearall">` line
2. Replace `form-control` with `idno-textarea` in the class string

```php
<?php

if (!empty($vars['unique_id'])) {
    $unique_id = $vars['unique_id'];
} else {
    $unique_id = 'body' . rand(0, 9999);
}
if (!empty($vars['class'])) {
    $class = $vars['class'];
} else {
    $class = '';
}
if (!empty($vars['height'])) {
    $height = $vars['height'];
} else {
    $height = 500;
}
if (!empty($vars['placeholder'])) {
    $placeholder = $vars['placeholder'];
} else {
    $placeholder = \Idno\Core\Idno::site()->language()->_('Share something brilliant...');
}
if (!empty($vars['value'])) {
    $value = $vars['value'];
} else {
    $value = '';
}
    $required = "";
if (!empty($vars['required'])) {
    $required = "required";
}
?>

<textarea name="<?php echo $vars['name']?>"  placeholder="<?php echo htmlspecialchars($placeholder);?>" style="height:<?php echo $height?>px"
          class="bodyInput mentionable idno-textarea <?php echo $class?>" id="<?php echo $unique_id?>" <?php echo $required; ?>><?php echo (htmlspecialchars($value)) ?></textarea>
<?php

// Expose this control to the api
$this->documentFormControl(
    $name, [
    'type' => 'longtext',
    'id' => $unique_id,
    'required' => !empty($required),
    'description' => $placeholder
    ]
);


// Prevent bonita leakage
foreach (['unique_id', 'class', 'height', 'placeholder', 'value', 'required', 'name', 'value'] as $var) {
    unset($this->vars[$var]);
}
```

Note: `$name` (not `$vars['name']`) in `documentFormControl()` is intentional — replicates the default which relies on Bonita variable extraction.

- [ ] **Step 2: Verify the change**

Diff against `templates/default/forms/input/longtext.tpl.php`. Differences:
- `<br class="clearall">` line removed
- `form-control` → `idno-textarea` in class attribute

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/forms/input/longtext.tpl.php
git commit -m "feat: add modern longtext.tpl.php override (form-control → idno-textarea)"
```

---

### Task 3: Create `forms/input/select.tpl.php` override

**Files:**
- Read: `templates/default/forms/input/select.tpl.php` (source to copy from)
- Create: `Themes/Twenty26/templates/modern/forms/input/select.tpl.php`

- [ ] **Step 1: Create the override file**

Create `Themes/Twenty26/templates/modern/forms/input/select.tpl.php`. Only change: the `class=` line — replace `input` with `idno-select` and drop the `input-select` fallback:

```php
<?php
// Define possible fields and their defaults, a boolean FALSE means don't show if not present
$fields_and_defaults = array(
    'name' => false,
    'id' => false,
    'autocomplete' => false,
    'autofocus' => false,
    'accept' => false,
    'checked' => false,
    'disabled' => false,
    'min' => false,
    'max' => false,
    'step' => false,
    'maxlength' => false,
    'multiple' => false,
    'pattern' => false,
    'readonly' => false,
    'required' => false,
    'src' => false,
    'spellcheck' => false,
    //'placeholder' => false,
    'accept' => false,
    'onclick' => false,
    'onfocus' => false,
    'onblur' => false,
    'onchange' => false,
);

if (!isset($vars['blank-default']) && (empty($vars['multiple'])))
    $vars['blank-default'] = true;

if (empty($vars['options']))
    $vars['options'] = [];

// We always want a unique ID
global $input_id;
if (!isset($vars['id'])) {
    $input_id ++;
    $vars['id'] = $vars['name'] . "_$input_id";
}

// Fudge multiple selectopr name
if (!empty($vars['multiple'])) {
    $vars['name'] = $vars['name'].'[]';
    $vars['class'] .= ' select-multiple';
}

// Handle multiple values
if (!is_array($vars['value'])) {
    $vars['value'] = [$vars['value']];
}

?>
<select
<?php

$published = [
    'type' => 'select',
    'multiple' => !empty($vars['multiple'])
];
if (isset($vars['placeholder']))
    $published['placeholder'] = $vars['placeholder'];
if (isset($vars['alt']))
    $published['alt'] = $vars['alt'];
if (isset($vars['description']))
    $published['description'] = $vars['description'];

foreach ($fields_and_defaults as $field => $default) {
    if (isset($vars[$field])) {
        if ($vars[$field] === true) {
            echo "$field ";
            $published[$field] = true;
        } else {
            echo "$field=\"{$vars[$field]}\" ";
            $published[$field] = $vars[$field];
        }
    }
    else {
        if ($default !== false) {
            if ($default === true) {
                echo "$field ";
                $published[$field] = true;
            } else {
                echo "$field=\"$default\" ";
                $published[$field] = $default;
            }
        }
    }
}
?>
    class="idno-select <?php echo isset($vars['class']) ? $vars['class'] : ''; ?>">
    <?php if (!empty($vars['blank-default'])) { ?>
    <option></option>
    <?php } ?>
    <?php
    foreach ($vars['options'] as $option => $label) {
        ?>
    <option value="<?php echo $option; ?>" <?php if (in_array($option, $vars['value'])) echo 'selected' ?>><?php echo htmlentities($label, ENT_QUOTES, 'UTF-8'); ?></option>
        <?php
    }
    ?>
</select>
<?php
// Ensure this is documented in the api get
if (!empty($published['placeholder'])) {
    $published['description'] = $published['placeholder'];
    unset($published['alt']); unset($published['placeholder']);
}

// Document form
$this->documentFormControl($vars['name'], $published);

// Prevent bonita polution
foreach (array_merge($fields_and_defaults, ['placeholder' => false, 'value' => '', 'options' => '', 'blank-default' => '', 'class' => '']) as $field => $default)
    unset($this->vars[$field]);
```

- [ ] **Step 2: Verify the change**

Diff against `templates/default/forms/input/select.tpl.php`. Differences:
- Class line: `input` → `idno-select`, fallback `'input-select'` → `''`

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/forms/input/select.tpl.php
git commit -m "feat: add modern select.tpl.php override (input → idno-select)"
```

---

## Chunk 2: Image File Upload

### Task 4: Create `forms/input/image-file.tpl.php` override

**Files:**
- Read: `templates/default/forms/input/image-file.tpl.php` (source to copy from)
- Read: `templates/default/forms/input/file.tpl.php` (to understand delegation)
- Create: `Themes/Twenty26/templates/modern/forms/input/image-file.tpl.php`

- [ ] **Step 1: Create the override file**

Create `Themes/Twenty26/templates/modern/forms/input/image-file.tpl.php`. Changes from default:
1. Wrap existing photos in `<div class="idno-image-existing">` for delete overlay positioning
2. Replace `fa fa-trash-o` icon in `createLink()` with inline trash SVG
3. Add `idno-image-delete` class to the delete control span
4. Replace `<span class="btn btn-primary btn-file">` with `<label class="idno-btn idno-btn-ghost" for="...">` (full-width)
5. Replace `fa fa-camera` with inline camera SVG
6. Hide the file input via CSS (`.image-file-input input[type="file"] { display: none; }`) since the `style` key is not in `$fields_and_defaults` and won't be emitted by the attribute loop
7. Update the class passed to nested file input: `'input-file form-control col-md-9'` → `'input-file'`

```php
<?php

if (empty($vars['id'])) {
    $vars['id'] = 'photo-' . md5(rand());
}

    $multiple = false;
if (strpos($vars['name'], '[]') !== false) {
    $multiple = true;
}

    $hide_existing = false;
if (!empty($vars['hide-existing'])) {
    $hide_existing = true;
}
?>
<div class="image-file-input">
    <div class="photo-preview-existing">
        <?php
        if (!empty($vars['object']->_id) && !$hide_existing) {

            $attachments = $vars['object']->getAttachments();
            foreach ($attachments as $attachment) {
                $filename = $attachment['filename'];

                $mainsrc = $attachment['url'];
                if (!empty($vars['object']->thumbs_large) && !empty($vars['object']->thumbs_large[$filename])) {
                    $src = $vars['object']->thumbs_large[$filename]['url'];

                    // Old style
                } else if (!empty($vars['object']->thumbnail_large)) {
                    $src = $vars['object']->thumbnail_large;

                    // Really old style
                } else if (!empty($vars['object']->thumbnail)) { // Backwards compatibility
                    $src = $vars['object']->thumbnail;

                    // Fallback
                } else {
                    $src = $mainsrc;
                }

                // Patch to correct certain broken URLs caused by https://github.com/idno/idno/issues/526
                $src = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $src);
                $mainsrc = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $mainsrc);

                $src = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($src);
                $mainsrc = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($mainsrc);
                ?>
                <div class="idno-image-existing">
                    <?php if ($vars['object']->canEdit() && empty($vars['hide-delete'])) { ?>
                    <span class="idno-image-delete delete-control">
                        <?php echo \Idno\Core\Idno::site()->actions()->createLink(
                            \Idno\Core\Idno::site()->config()->getDisplayURL() . 'attachment/' . $vars['object']->getId() . '/' . $attachment['_id'] . '/',
                            '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
                            [],
                            [
                                    'method' => 'POST',
                                    'class' => 'edit',
                                    'confirm' => true,
                                    'confirm-text' => \Idno\Core\Idno::site()->language()->_("Are you sure you want to permanently delete this?")
                            ]
                        ); ?>
                    </span>
                    <?php } ?>
                    <img src="<?php echo $this->makeDisplayURL($src) ?>" class="existing"/>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="photo-preview" id="<?php echo $vars['id']; ?>_preview">
        <img id="<?php echo $vars['id']; ?>_img" src="" class="preview" style="display:none; width: 400px;" />
    </div>
    <p>
        <label class="idno-btn idno-btn-ghost" for="<?php echo $vars['id']; ?>" style="width:100%;justify-content:center;cursor:pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            <span class="photo-filename" data-nexttext="<?php echo \Idno\Core\Idno::site()->language()->_('Choose different photo'); ?>">
                <?php
                if (empty($vars['object']->_id)) {
                    echo \Idno\Core\Idno::site()->language()->_('Select a photo');
                } else {
                    if (!$multiple) {
                        echo \Idno\Core\Idno::site()->language()->_('Choose different photo');
                    } else {
                        echo \Idno\Core\Idno::site()->language()->_('Add photo');
                    }
                }
                ?>
            </span>
            <?php echo
            $this->__(
                [
                'name' => $vars['name'],
                'id' => $vars['id'],
                'accept' => 'image/*',
                'onchange' => 'Template.activateImagePreview(this)',
                'class' => 'input-file']
            )->draw('forms/input/file');
            ?>
        </label>
    </p>
</div>
```

- [ ] **Step 2: Verify the changes**

Compare against `templates/default/forms/input/image-file.tpl.php`. Key differences:
- `<div class="existing-photo">` → `<div class="idno-image-existing">`
- `<span class="delete-control">` → `<span class="idno-image-delete delete-control">`
- `<i class="fa fa-trash-o"></i>` → inline trash SVG
- `<span class="btn btn-primary btn-file">` → `<label class="idno-btn idno-btn-ghost" for="...">`
- `<i class="fa fa-camera"></i>` → inline camera SVG
- `'class' => 'input-file form-control col-md-9'` → `'class' => 'input-file'`

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/forms/input/image-file.tpl.php
git commit -m "feat: add modern image-file.tpl.php override (Bootstrap + FA → idno-btn + SVG)"
```

---

## Chunk 3: Access Control Dropdown + CSS

### Task 5: Create `content/access.tpl.php` override

**Files:**
- Read: `templates/default/content/access.tpl.php` (source to copy from)
- Create: `Themes/Twenty26/templates/modern/content/access.tpl.php`

This is the most complex override. It replaces the Bootstrap dropdown with an Alpine.js component.

- [ ] **Step 1: Create the override file**

Create `Themes/Twenty26/templates/modern/content/access.tpl.php`:

```php
<?php

    $access = 'PUBLIC';
if (!empty($vars['object'])) {
    if (!empty($vars['object']->access)) {
        $access = $vars['object']->access;
    }
}
if (!empty($vars['default-access'])) {
    $access = $vars['default-access'];
}

    $id_code = 'acl-' . md5(mt_rand());

    // Build the options list for Alpine.js
    $current_user_uuid = \Idno\Core\Idno::site()->session()->currentUserUUID();

    // Map access value to initial label and icon
    $initial_label = \Idno\Core\Idno::site()->language()->_('Public');
    $initial_icon = 'globe';

    $access_options = [
        ['value' => 'PUBLIC', 'label' => \Idno\Core\Idno::site()->language()->_('Public'), 'icon' => 'globe'],
        ['value' => 'SITE', 'label' => \Idno\Core\Idno::site()->language()->_('Members only'), 'icon' => 'users'],
        ['value' => $current_user_uuid, 'label' => \Idno\Core\Idno::site()->language()->_('Private'), 'icon' => 'lock'],
    ];

    // Add custom access groups
    $acls = \Idno\Entities\AccessGroup::get(array('owner' => $current_user_uuid));
    if (!empty($acls)) {
        foreach ($acls as $acl) {
            $icon = ($acl->access_group_type == 'FOLLOWING') ? 'users' : 'cog';
            $access_options[] = ['value' => $acl->getUUID(), 'label' => $acl->title, 'icon' => $icon];
        }
    }

    // Determine initial label/icon from current access value
    foreach ($access_options as $opt) {
        if ($opt['value'] === $access) {
            $initial_label = $opt['label'];
            $initial_icon = $opt['icon'];
            break;
        }
    }

if (!empty(\Idno\Core\Idno::site()->config()->show_privacy) || $access != 'PUBLIC') {

    ?>
        <div class="access-control-block">
            <input type="hidden" name="access" id="access-control-id-<?php echo $id_code; ?>" value="<?php echo htmlspecialchars($access); ?>"/>

            <div class="idno-access-dropdown" x-data="{ open: false, selected: <?php echo json_encode($access); ?>, label: <?php echo json_encode($initial_label); ?>, icon: <?php echo json_encode($initial_icon); ?> }">
                <button type="button" class="idno-access-trigger" @click="open = !open">
                    <template x-if="icon === 'globe'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </template>
                    <template x-if="icon === 'users'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </template>
                    <template x-if="icon === 'lock'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </template>
                    <template x-if="icon === 'cog'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </template>
                    <span x-text="label"></span>
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </button>

                <div class="idno-access-menu" x-show="open" @click.away="open = false" x-cloak>
                    <?php foreach ($access_options as $opt) { ?>
                    <button type="button"
                            class="idno-access-option"
                            :class="{ 'active': selected === <?php echo json_encode($opt['value']); ?> }"
                            @click="selected = <?php echo json_encode($opt['value']); ?>; label = <?php echo json_encode($opt['label']); ?>; icon = <?php echo json_encode($opt['icon']); ?>; document.getElementById('access-control-id-<?php echo $id_code; ?>').value = <?php echo json_encode($opt['value']); ?>; open = false">
                        <?php if ($opt['icon'] === 'globe') { ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <?php } elseif ($opt['icon'] === 'users') { ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <?php } elseif ($opt['icon'] === 'lock') { ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <?php } else { ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <?php } ?>
                        <?php echo htmlspecialchars($opt['label']); ?>
                        <svg class="check" x-show="selected === <?php echo json_encode($opt['value']); ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                    </button>
                    <?php } ?>
                </div>
            </div>

        </div>

    <?php

} else {

    ?>
        <input type="hidden" name="access" id="access-control-id-<?php echo $id_code; ?>" value="<?php echo htmlspecialchars($access); ?>"/>
        <?php

}

    /**
 * Document the control for the api
*/
    $this->documentFormControl(
        'access', [
        'id' => 'access-control-id-' .$id_code,
        'description' => 'Access control',
        ]
    );
```

- [ ] **Step 2: Verify key behaviors**

Check the template for:
1. `$access` resolution logic matches default exactly
2. `$id_code` generation matches default
3. `show_privacy` conditional matches default
4. `documentFormControl()` is OUTSIDE the if/else (runs always)
5. Hidden input has same name/id/value as default
6. `access-control-block` wrapper class preserved
7. Each access option correctly maps value → label → icon
8. Alpine.js sets `document.getElementById('access-control-id-{id_code}').value` on click
9. Initial state correctly derived from `$access` variable
10. Custom access groups from `AccessGroup::get()` included with correct icon logic

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/templates/modern/content/access.tpl.php
git commit -m "feat: add modern access.tpl.php override (Bootstrap dropdown → Alpine.js + SVG)"
```

---

### Task 6: Add new CSS to `forms.css`

**Files:**
- Modify: `Themes/Twenty26/src/css/components/forms.css` (append before closing `}` of `@layer components`)

- [ ] **Step 1: Add access dropdown CSS**

Append the following CSS inside the `@layer components { }` block in `Themes/Twenty26/src/css/components/forms.css`, after the existing `.idno-tag-remove:hover` rule:

```css
  /* Alpine.js cloak — hide elements until Alpine initializes */
  [x-cloak] {
    display: none !important;
  }

  /* Access control dropdown (Alpine.js) */
  .idno-access-dropdown {
    position: relative;
    display: inline-block;
  }

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

  .idno-access-trigger svg {
    width: 14px;
    height: 14px;
  }

  .idno-access-trigger .chevron {
    width: 10px;
    height: 10px;
    opacity: 0.4;
  }

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

  .idno-access-option:hover {
    background: var(--color-bg);
  }

  .idno-access-option:focus-visible {
    background: var(--color-bg);
    outline: none;
  }

  .idno-access-option.active {
    background: var(--color-bg);
    font-weight: 600;
  }

  .idno-access-option svg {
    width: 16px;
    height: 16px;
    color: var(--color-text-secondary);
    flex-shrink: 0;
  }

  .idno-access-option .check {
    width: 14px;
    height: 14px;
    margin-left: auto;
    color: var(--color-primary);
  }

  /* Image file upload */
  .image-file-input input[type="file"] {
    display: none;
  }

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
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .idno-image-delete:hover {
    background: rgba(220, 38, 38, 0.8);
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

- [ ] **Step 2: Verify CSS structure**

Read back `forms.css` and verify:
1. New rules are inside `@layer components { }`
2. All values use CSS custom properties (no hardcoded colors)
3. `:focus-visible` present on `.idno-access-trigger`, `.idno-access-option`, `.idno-image-delete`
4. No duplicate class definitions

- [ ] **Step 3: Commit**

```bash
git add Themes/Twenty26/src/css/components/forms.css
git commit -m "feat: add access dropdown and image upload CSS to forms.css"
```

---

### Task 7: Build and verify

**Files:**
- Read: `Themes/Twenty26/package.json` (for build command)

- [ ] **Step 1: Run the build**

```bash
cd Themes/Twenty26 && npm run build
```

Expected: Build succeeds, `dist/modern.min.css` and `dist/modern.min.js` are updated.

- [ ] **Step 2: Commit the dist files**

```bash
git add Themes/Twenty26/dist/
git commit -m "chore: rebuild dist with edit form template overrides"
```

- [ ] **Step 3: Manual browser verification**

Open `https://idno:8890/` in browser. Test each form:

1. **Status edit** — click "New Status" or edit an existing status
   - Textarea should be styled (not raw unstyled)
   - Access control dropdown should show globe icon + "Public"
   - Clicking dropdown should open upward with three options + any custom groups
   - Selecting an option should update trigger text and icon
   - Publish/Save button should work

2. **Photo edit** — click "New Photo" or edit an existing photo
   - File upload button should show camera icon + "Select a photo"
   - If editing existing, photo should display with trash overlay on hover
   - Caption textarea styled
   - Tags input styled

3. **Entry edit** — click "New Entry" or edit an existing entry
   - Title input styled
   - Rich text editor should still work (Tiptap, separate override)
   - Access dropdown functional

4. **Checkin edit** — if Checkin plugin enabled
   - Location input styled
   - All form elements styled

For each form, verify:
- No unstyled Bootstrap classes visible (no `form-control`, `btn-group`, `fa fa-*` text)
- Focus states work (tab through elements, see focus rings)
- Access dropdown updates the hidden input (inspect with browser dev tools)
