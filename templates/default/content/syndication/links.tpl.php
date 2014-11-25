<?php

    if ($posse = $vars['object']->getPosseLinks()) {

?>
<div class="posse">
    <a name="posse"></a>

    <p>
        Also on:
        <?php

            foreach ($posse as $service => $posse_link) {
                if (is_array($posse_link)) {
                    foreach($posse_link as $element) {
                        echo '<a href="' . $element['url'] . '" rel="syndication" class="u-syndication ' . $service . '">' . $element['identifier'] . '</a> ';
                    }
                } else {
                    echo '<a href="' . $posse_link . '" rel="syndication" class="u-syndication ' . $service . '">' . $service . '</a> ';
                }
            }

        ?>
    </p>
</div>
<?php

    }