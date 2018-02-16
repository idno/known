<?php
$dataurl = "";
if (!empty($vars['data-url']))
    $dataurl = htmlentities($vars['data-url']);

if (empty($vars['object']->hide_preview)) { ?>
<div class="unfurl-block" data-parent-object="<?= $vars['object']->_id; ?>">
    <div class="unfurl col-md-12" style="display:none;" data-url="<?= $dataurl; ?>"></div>
    <?php if ($vars['object']->canEdit() && (!empty($vars['object']->_id))) { ?>
    <div class="unfurl-edit pull-right small">
        <a href="#" class="refresh"><i class="fa fa-refresh"></i> <?= \Idno\Core\Idno::site()->language()->_('Refresh preview'); ?></a> &nbsp;
        <a href="#" class="delete"><i class="fa fa-trash"></i> <?= \Idno\Core\Idno::site()->language()->_('Remove preview'); ?></a>
    </div>
    <?php } ?>
</div>
<?php } ?>
<?php
    // clean up
    unset($this->vars['data-url']);
?>