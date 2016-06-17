
    <div>
        <?php

            if ($vars['object']->canEdit()) {

        ?>
       <div class="row edit-page-actions">
        <div class="col-md-12">
        <span class="page-action">
            <i class="fa fa-cog"></i> <a href="<?=\Idno\Core\Idno::site()->config()->getURL()?>admin/staticpages/">Manage pages</a>
        </span>
	    <?php

    if ($vars['object']->canEdit()) {

?>

        <span class="page-action"><i class="fa fa-pencil"></i> <a href="<?=$vars['object']->getEditURL()?>" class="edit">Edit</a></span>
        <span class="page-action"><i class="fa fa-trash-o"></i>
        <?=  \Idno\Core\Idno::site()->actions()->createLink($vars['object']->getDeleteURL(), 'Delete', array(), array('method' => 'POST', 'class' => 'edit', 'confirm' => true, 'confirm-text' => 'Are you sure you want to permanently delete this entry?'));?></span>

<?php

    }

?>	    	    
    </div>
        </div>
        
        
        <?php

            }

            if (empty($vars['object']->hide_title)) {
        ?>
        <h1 class="p-name" style="margin-bottom: 1em"><?=$vars['object']->getTitle()?></h1>
        <?php
            }

            if (!empty($vars['object']->forward_url)) {

                ?>
                <h2>
                    You are seeing this page because you are a site administrator. Other users will be forwarded
                    to <a href="<?=$vars['object']->forward_url?>"><?=$vars['object']->forward_url?></a>.
                </h2>
                <?php

            }

        ?>
        <?php echo $this->autop($this->parseURLs($this->parseHashtags($vars['object']->body))); ?>

    </div>
