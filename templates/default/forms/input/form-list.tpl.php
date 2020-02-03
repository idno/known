<?php

/* 
 * List form elements into a nice structure
 */

$elements = $vars['form'];
$labels = $vars['labels'];
$placeholders = $vars['placeholders'];
$defaults = $vars['defaults'];
$required = $vars['required'];
$values   = $vars['values'];

?>

<div class="form-list">
    
    <?php 
    foreach ($elements as $field => $type) {
        ?>
        
    <div class="row">
        <div class="col-sm-12 col-md-3">
            <label>
                <?= \Idno\Core\Idno::site()->language()->_($labels[$field]); ?>
            </label> 
        </div>
        <div class="col-sm-12 col-md-9">
            <?php
            $value = (object)$values;
            $settings = [
                'name' => $field,
                'id' => 'form-list-element-' . $field,
                'value' => $value->$field??$defaults[$field],
            ];
            if (!empty($required[$field])) {
                $settings['required'] = true;
            }
            if (!empty($placeholders[$field])) {
                $settings['placeholder'] = $placeholders[$field];
            }
            ?>
            <?= $this->__($settings)->draw('forms/input/' . $type); ?>
        </div>
    </div>
        
        <?php
    }
    ?>
    
    
</div>
