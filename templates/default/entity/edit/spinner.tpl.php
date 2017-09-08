<?php
if (empty($vars['id']))
    $vars['id'] = 'spinner_' . md5(rand());
?>
<div id="<?= $vars['id']; ?>" class="spinner" style="display:none">
  <div class="bounce1"></div>
  <div class="bounce2"></div>
  <div class="bounce3"></div>
</div>
<?php 
unset($this->vars['id']);
?>