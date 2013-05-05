<?php

    if (!empty($vars['contentTypes']) && is_array($vars['contentTypes'])) {

?>
        <div class="contentTypeButtonBar">
<?php

        foreach($vars['contentTypes'] as $contentType) {
            /* @var Idno\Common\ContentType $contentType */
?>

                <a class="contentTypeButton" id="<?=$contentType->getIDSelector()?>Button" data-icon="<?=$contentType->getIcon()?>" href="#">
                    <?=$contentType->getTitle()?>
                </a>

<?php

        }

?>
            <br clear="all" style="line-height: 0em" />
        </div>
<?php

    }

?>