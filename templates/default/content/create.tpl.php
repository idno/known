<?php

    if (empty($vars['items']) && sizeof($vars['contentTypes']) <= 1 &&
        \Idno\Core\site()->session()->isLoggedIn() &&
        \Idno\Core\site()->session()->currentUser()->isAdmin()) {

        ?>
        <div class="row" style="margin-top: 5em">
            <div class="span6 offset3">
                <div class="welcome">
                    <p>
                        <a href="http://idno.co" target="_blank"><img src="http://idno.co/idno.png" style="width: 4em; border: 0"></a>
                    </p>
                    <p>
                        Welcome to your idno site!<br />
                        <a href="<?=\Idno\Core\site()->config()->url?>admin/">Click here to start configuring your site</a>.
                    </p>
                    <p>
                        You can add new kinds of content from <a href="<?=\Idno\Core\site()->config()->url?>admin/plugins/">the plugins menu</a><?php

                            if (sizeof($vars['contentTypes']) == 1) {
                                ?>, or get started by clicking on the icon below:<?php
                            }

                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php

    }

    if (!empty($vars['contentTypes']) && is_array($vars['contentTypes'])) {

        ?>
        <div class="buttonBar">
        <div class="row ">
        <div class="span12">
        <div id="contentTypeButtonBar">
            <?php

                foreach ($vars['contentTypes'] as $contentType) {
                    /* @var Idno\Common\ContentType $contentType */
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
\Idno\Core\site()->session()->isLoggedIn() &&
\Idno\Core\site()->session()->currentUser()->isAdmin()) {

?>

        <div class="row">
            <div class="span6 offset3">
                <div class="welcome">
                    <p>
                        You can always get more plugins, support and other materials from
                        <a href="http://idno.co" target="_blank">the official idno site at idno.co</a>.
                    </p>
                </div>
            </div>
        </div>

<?php

    }

?>