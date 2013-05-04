<?php

    if (!empty($vars['contentTypes']) && is_array($vars['contentTypes'])) {
        foreach($vars['contentTypes'] as $contentType) {
            /* @var Idno\Common\ContentType $contentType */
?>

            <div class="contentTypeButton" id="<?=$contentType->getIDSelector()?>Button">
                <p>

                </p>
            </div>

<?php

        }
    }

?>