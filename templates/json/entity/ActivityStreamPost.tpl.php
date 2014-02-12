<?php 
    $object = $vars['object'];
    $subObject = $object->getObject();
    /* @var \Idno\Entities\ActivityStreamPost $object */
    /* @var \Idno\Common\Entity $subObject */

    if (!empty($object) && !empty($subObject)) {
        echo json_encode($object);
    }
?>