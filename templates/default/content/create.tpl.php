<?php

    if (!empty($vars['contentTypes']) && is_array($vars['contentTypes'])) {

        ?>
        <div class="buttonBar">
        <div class="row ">
        <div class="span12">
        <div id="contentTypeButtonBar">
            <?php

                foreach ($vars['contentTypes'] as $contentType) {
                    /* @var known\Common\ContentType $contentType */
                    ?>

                    <a class="contentTypeButton" id="<?= $contentType->getClassSelector() ?>Button"
                       href="<?= $contentType->getEditURL() ?>"
                       onclick="contentCreateForm('<?= $contentType->camelCase($contentType->getEntityClassName()) ?>'); return false;">
                        <span class="contentTypeLogo"><?= $contentType->getIcon() ?></span>
                        <?= $contentType->getTitle() ?>
                    </a>

                <?php

                }

            ?>
            <br clear="all" style="line-height: 0em"/>
        </div>
    <?php

    }

?>
    </div>
    </div>
    <div class="row">
        <div class="span12">
            <div id="contentCreate"></div>
        </div>
    </div>
</div>
<?php

    if (empty($vars['items']) && sizeof($vars['contentTypes']) <= 1 &&
        \known\Core\site()->session()->isLoggedIn() &&
        \known\Core\site()->session()->currentUser()->isAdmin()) {

        ?>
        <div class="row" style="margin-top: 5em">
            <div class="span6 offset3">
                <div class="welcome">
                    <p>
                        <a href="http://withknown.com/" target="_blank"><img src="http://withknown.com/img/logo_k.png" style="width: 4em; border: 0"></a>
                    </p>
                    <p>
                        Welcome to your Known site!<br />
                        <a href="<?=\known\Core\site()->config()->url?>admin/">Click here to start configuring your site</a>.
                    </p>
                </div>
            </div>
        </div>

<?php

    }

?>