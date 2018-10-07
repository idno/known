<?php

$currentPage = \Idno\Core\Idno::site()->currentPage();
$pageOwner = $currentPage->getOwner();

if (!empty($vars['object'])) {
    $objectIcon = $vars['object']->getIcon();
} else {
    $objectIcon = false;
}

$jsonld = null;

if ($currentPage->isPermalink()) {
    if (!empty($vars['object'])) {
    
        $object = $vars['object'];
        if (is_callable([$object, 'jsonLDSerialise'])) {
        
            $jsonld = $object->jsonLDSerialise();
            
        }
    }
}

if (!empty($jsonld)) {
    ?>
<!-- JSON+LD Structured Data -->
<script type="application/ld+json">
    <?= json_encode($jsonld, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>
</script>
<?php
}