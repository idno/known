<div class="idno-content">
    <?php
        $this->annotations = [$vars['permalink'] => $vars['annotation']];

        echo $this->draw('entity/annotations/' . $vars['subtype']);
    ?>
</div>