<?php 

    /**
     * idno host-meta file
     * 
     * Used to support webfinger and (potentially) all sorts of other goodness.
     * 
     * @package idno
     * @subpackage core
     */

    echo "<?xml version='1.0' encoding='UTF-8'?>\n"; 

    require_once(dirname(dirname(dirname(__FILE__))) . '/idno/start.php');
    
?>
<XRD xmlns='http://docs.oasis-open.org/ns/xri/xrd-1.0'
     xmlns:hm='http://host-meta.net/xrd/1.0'>
 
    <hm:Host><?=$_SERVER['SERVER_NAME']?></hm:Host>
 
    <Link rel='lrdd'
          template='<?=\Idno\Core\site()->config()->url?>describe/?uri={uri}'>
        <Title>Resource Descriptor</Title>
    </Link>
</XRD>
