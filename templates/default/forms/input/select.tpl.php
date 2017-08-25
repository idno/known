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

// We always want a unique ID
global $input_id;
if (!isset($vars['id'])) {
    $input_id ++;
    $vars['id'] = $vars['name'] . "_$input_id";
}
?>
<select
<?php

$published = [
    'type' => 'select'
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
    class="input <?php echo isset($vars['class']) ? $vars['class'] : 'input-select'; ?>"
    value="<?php if (isset($vars['value'])) echo htmlentities($vars['value'], ENT_QUOTES, 'UTF-8'); ?>"> 
    <?php if (!empty($vars['blank-default'])) { ?>
    <option></option>
    <?php } ?>
    <?php 
    foreach ($vars['options'] as $option => $label) {
        ?>
    <option value="<?= $option; ?>" <?php if ($value == $option) echo 'selected' ?>><?= htmlentities($label, ENT_QUOTES, 'UTF-8'); ?></option>
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
foreach (array_merge($fields_and_defaults, ['placeholder' => false, 'value' => '', 'options' => '', 'blank-default']) as $field => $default) 
    unset($this->vars[$field]);
?>