<?php

/*
 * List form elements into a nice structure
 */

$elements = $vars['form'];
$labels = $vars['labels'];
$placeholders = $vars['placeholders'];
$help = $vars['help'];
$defaults = $vars['defaults'];
$required = $vars['required'];
$values   = $vars['values'];

?>

<div class="form-list">
    
    <?php
    foreach ($elements as $field => $type) {

        $value = (object)$values;
        $settings = [
            'name' => $field,
            'id' => 'form-list-element-' . $field,
            'value' => $value->$field??$defaults[$field],
        ];
        if (in_array($field, $required)) {
            $settings['required'] = true;
        }
        if (!empty($placeholders[$field])) {
            $settings['placeholder'] = $placeholders[$field];
        }


        if ($type != 'hidden') {
            ?>
        
    <div class="row">
        <div class="col-sm-12 col-md-2">
            <label>
                <?php echo \Idno\Core\Idno::site()->language()->_($labels[$field]); ?>
            </label> 
        </div>
        <div class="col-sm-12 col-md-6">  
            <?php echo $this->__($settings)->draw('forms/input/' . $type); ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?php if (!empty($help[$field])) { ?>
            <p class="config-desc"><?php echo \Idno\Core\Idno::site()->language()->_($help[$field]); ?></p>
            <?php } ?>
        </div>
    </div>
        
            <?php
        } else {
            echo $this->__($settings)->draw('forms/input/' . $type);
        }
    }
    ?>
    
    
</div>
