<?php

    $object = $vars['object'];
    /*
     * @var \Idno\Common\Entity $object
     */

?>

<div>

    <h2 class="p-name">
        <?php if (empty($vars['feed_view'])) { ?>
            <a href="<?php echo $object->getURL() ?>"><?php echo $object->getTitle() ?></a>
        <?php } ?>
    </h2>

    <span class="p-location u-checkin h-card">
        <data class="p-name" value="<?php echo $object->placename ?>"></data>
        <data class="p-latitude" value="<?php echo $object->lat() ?>"></data>
        <data class="p-longitude" value="<?php echo $object->long() ?>"></data>
    </span>

    <?php if (empty($vars['feed_view'])) { ?>
        <div id="map_<?php echo $object->_id ?>" style="height: 250px"></div>
    <?php } ?>

    <div class="e-content entry-content">
        <?php
        if (!empty($object->body)) {
            echo $this->autop($this->parseURLs($this->parseHashtags($object->body)));
        }

        if (!empty($object->tags)) {
            echo $this->__(['tags' => $object->tags])->draw('forms/output/tags');
        } ?>
    </div>

</div>
<?php if (empty($vars['feed_view'])) { ?>
        <script>
            var map<?php echo $object->_id?> = L.map('map_<?php echo $object->_id?>', {
                touchZoom: false,
                scrollWheelZoom: false
            }).setView([<?php echo $object->lat() ?>, <?php echo $object->long() ?>], 16);
            var layer<?php echo $object->_id?> = new L.StamenTileLayer("toner-lite");
            map<?php echo $object->_id?>.addLayer(layer<?php echo $object->_id?>);
            var marker<?php echo $object->_id?> = L.marker([<?php echo $object->lat()?>, <?php echo $object->long()?>]);
            marker<?php echo $object->_id?>.addTo(map<?php echo $object->_id?>);
            //map<?php echo $object->_id?>.zoomControl.disable();
            map<?php echo $object->_id?>.scrollWheelZoom.disable();
            map<?php echo $object->_id?>.touchZoom.disable();
            map<?php echo $object->_id?>.doubleClickZoom.disable();
        </script>
<?php }
