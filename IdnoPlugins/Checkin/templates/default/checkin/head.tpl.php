<link rel="stylesheet" href="<?php echo \Idno\Core\site()->config()->url ?>IdnoPlugins/Checkin/external/leaflet/leaflet.css"/>
<!--[if lte IE 8]>
<link rel="stylesheet" href="<?php echo \Idno\Core\site()->config()->url ?>IdnoPlugins/Checkin/external/leaflet/leaflet.ie.css"/>
<![endif]-->
<script type="text/javascript" src="<?php echo \Idno\Core\site()->config()->url ?>IdnoPlugins/Checkin/external/leaflet/leaflet.js"></script>
<script type="text/javascript" src="<?php

    if (strpos(\Idno\Core\site()->config()->url, 'https') !== false) {
        echo 'https://stamen-tiles.a.ssl.fastly.net';
    } else {
        echo 'http://maps.stamen.com';
    }

?>/js/tile.stamen.js?v1.2.2"></script>

