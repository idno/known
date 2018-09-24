<?php
$dataurl = "";
if (!empty($vars['data-url']))
    $dataurl = htmlentities($vars['data-url']);

if (empty($vars['object']->hide_preview)) { ?>
<div class="unfurl-block" data-parent-object="<?php echo $vars['object']->_id; ?>">
    <div class="unfurl col-md-12" style="display:none;" data-url="<?php echo $dataurl; ?>"></div>
    <?php if ($vars['object']->canEdit() && (!empty($vars['object']->_id))) { ?>
    <div class="unfurl-edit pull-right small">
        <a href="#" class="refresh" title="<?php echo htmlentities(\Idno\Core\Idno::site()->language()->_('Refresh preview'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-sync"></i></a>
        <a href="#" class="delete" title="<?php echo htmlentities(\Idno\Core\Idno::site()->language()->_('Remove preview'), ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-trash"></i></a>
    </div>
    <?php } ?>
</div>
<?php } ?>
<?php
    // clean up
    unset($this->vars['data-url']);
