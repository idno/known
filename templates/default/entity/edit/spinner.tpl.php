<?php
if (empty($vars['id']))
    $vars['id'] = 'spinner_' . md5(rand());

if (!isset($vars['start-hidden']))
    $vars['start-hidden'] = true;
?>
<div id="<?= $vars['id']; ?>" class="spinner" <?php if ($vars['start-hidden'] === true) { ?>style="display:none"<?php } ?>>
  <div class="bounce1"></div>
  <div class="bounce2"></div>
  <div class="bounce3"></div>
</div>
<?php 
unset($this->vars['id']);
?>