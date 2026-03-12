<?php

if (!empty($vars['contentTypes']) && is_array($vars['contentTypes'])) {

?>
<div class="idno-content-type-bar">
    <?php
    foreach ($vars['contentTypes'] as $contentType) {
        /* @var \Idno\Common\ContentType $contentType */
    ?>
        <a class="idno-content-type-tab"
           href="<?= $contentType->getEditURL() ?>">
            <span class="idno-content-type-icon"><?= $contentType->getIcon() ?></span>
            <?= $contentType->getTitle() ?>
        </a>
    <?php
    }
    ?>
</div>
<?php

}

?>
