<?php

    $object = $vars['object'];
    /*
     * @var \Idno\Common\Entity $object
     */

?>

<div class="">

    <p class="h-geo">
        <a href="<?=$object->getURL()?>"><?=$object->getTitle()?></a>
        <data class="p-latitude" value="<?=$object->lat?>"></data>
        <data class="p-longitude" value="<?=$object->long?>"></data>
    </p>
    <?php
        if (!empty($object->body)) {
            echo $this->autop($this->parseURLs($this->parseHashtags($object->body)));
        }
    ?>
    <div id="map_<?=$object->_id?>" style="height: 250px"></div>

</div>
<script>
    var map<?=$object->_id?> = L.map('map_<?=$object->_id?>').setView([<?=$object->lat?>, <?=$object->long?>], 13);
    var layer<?=$object->_id?> = new L.StamenTileLayer("toner-lite");
    map<?=$object->_id?>.addLayer(layer<?=$object->_id?>);
    var marker<?=$object->_id?> = L.marker([<?=$object->lat?>, <?=$object->long?>]);
    marker<?=$object->_id?>.addTo(map<?=$object->_id?>);
</script>