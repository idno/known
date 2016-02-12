<?php

    if ($posse = $vars['object']->getPosseLinks()) {

?>
<div class="posse">
    <a name="posse"></a>

    <p>
        Also on:
        <?php

            foreach ($posse as $service => $posse_links) {
                if (is_string($posse_links)) {
                    $posse_links = [['url' => $posse_links, 'identifier' => $service]];
                }

                foreach($posse_links as $element) {
                    $this->username = isset($element['account_id']) ? $element['account_id'] : false;
                    $human_icon = $this->draw('content/syndication/icon/' . $service);
                    if (empty($human_icon)) {
                        $human_icon = $this->draw('content/syndication/icon/generic');
                    }

                    echo '<a href="' . $element['url'] . '" rel="syndication" class="u-syndication ' . $service . '">' . $human_icon . ' ' . $element['identifier'] . '</a> ';
                }
            }

        ?>
    </p>
</div>
<?php

    }