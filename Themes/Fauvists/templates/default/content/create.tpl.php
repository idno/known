<?php

if (!empty($vars['contentTypes']) && is_array($vars['contentTypes'])) {

    ?>
        <div class="buttonBar">
        <div class="row ">
        <div class="col-md-12">
        <div id="contentTypeButtonBar">
        <?php

        foreach ($vars['contentTypes'] as $contentType) {
            /* @var Idno\Common\ContentType $contentType */
            $entityType = $contentType->camelCase($contentType->getEntityClassName());
            ?>

                    <a class="contentTypeButton" id="<?php echo $contentType->getClassSelector() ?>Button"
                       href="<?php echo $contentType->getEditURL() ?>"
                       onclick="event.preventDefault(); contentCreateForm('<?php echo $entityType ?>', '<?php echo $contentType->getEditURL() ?>'); return false;">
                        <span class="contentTypeLogo"><?php echo $contentType->getIcon() ?></span>

                    </a>

                    <!--<?php echo $contentType->getTitle() ?>-->

                <?php

        }

        ?>
            <br class="clearall" style="line-height: 0em"/>
        </div>
    <?php

}

?>
    </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="contentCreate"></div>
        </div>
    </div>
</div>
<?php

if (empty($vars['items']) && sizeof($vars['contentTypes']) <= 1 &&
        \Idno\Core\Idno::site()->session()->isLoggedIn() &&
        \Idno\Core\Idno::site()->session()->currentUser()->isAdmin()) {

    ?>
        <div class="row" style="margin-top: 5em">
            <div class="col-md-6 col-md-offset-3">
                <div class="welcome">
                    <p>
                        <a href="https://withknown.com/" target="_blank"><img src="https://withknown.com/img/logo_k.png" style="width: 4em; border: 0"></a>
                    </p>
                    <p>
                        <?= \Idno\Core\Idno::site()->language()->_('Welcome to your Known site!'); ?><br />
                        <a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/"><?= \Idno\Core\Idno::site()->language()->_('Click here to start configuring your site'); ?></a>.
                    </p>
                </div>
            </div>
        </div>

    <?php

}

?>
<a name="feed"></a>