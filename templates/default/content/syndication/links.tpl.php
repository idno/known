<?php

    if ($posse = $vars['object']->getPosseLinks()) {

?>
<div class="posse">
    <a name="posse"></a>

    <p>
        Also on:
        <?php

            foreach ($posse as $service => $posse_link) {

                $human_icon = $this->draw('content/syndication/icon/' . $service);
                if (empty($human_icon)) {
                    $human_icon = $this->draw('content/syndication/icon/generic');
                }

                if (is_array($posse_link)) {
                    foreach($posse_link as $element) {
                        echo '<a href="' . $element['url'] . '" rel="syndication" class="u-syndication ' . $service . '">' . $human_icon . ' ' . $element['identifier'] . '</a> ';
                    }
                } else {
                    echo '<a href="' . $posse_link . '" rel="syndication" class="u-syndication ' . $service . '">' . $human_icon . ' ' . $service . '</a> ';
                }
            }

        ?>
    </p>
</div>
<?php

    }