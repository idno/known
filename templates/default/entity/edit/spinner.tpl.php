<?php
if (empty($vars['id'])) {
    $vars['id'] = 'spinner_' . md5(rand());
}

if (!isset($vars['start-hidden'])) {
    $vars['start-hidden'] = true;
}

if (empty($vars['class'])) {
    $vars['class'] = '';
}
?>
<div id="<?php echo $vars['id']; ?>" class="spinner <?php echo $vars['class']; ?>" <?php if ($vars['start-hidden'] === true) { ?>style="display:none"<?php
} ?>>
  <div class="bounce1"></div>
  <div class="bounce2"></div>
  <div class="bounce3"></div>
</div>
<?php
unset($this->vars['id']);
unset($this->vars['class']);
unset($this->vars['start-hidden']);
