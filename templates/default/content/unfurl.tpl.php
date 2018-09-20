<?php
$dataurl = "";
if (!empty($vars['data-url']))
    $dataurl = htmlentities($vars['data-url']);

if (empty($vars['object']->hide_preview)) { ?>
<div class="unfurl-block" data-parent-object="<?= $vars['object']->_id; ?>">
    <div class="unfurl col-md-12" style="display:none;" data-url="<?= $dataurl; ?>"></div>
    <?php if ($vars['object']->canEdit() && (!empty($vars['object']->_id))) { ?>
    <div class="unfurl-edit pull-right small">
        <a href="#" class="refresh" title="<?= htmlentities(\Idno\Core\Idno::site()->language()->_('Refresh preview'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-sync"></i></a>
        <a href="#" class="delete" title="<?= htmlentities(\Idno\Core\Idno::site()->language()->_('Remove preview'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-trash"></i></a>
    </div>
    <?php } ?>
</div>
<?php } ?>
<?php
    // clean up
    unset($this->vars['data-url']);
?>