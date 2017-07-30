<?php

if (empty($vars['object']) || empty($vars['object']->data))
    return;

$vars['id'] = "unfurled-url-" . $vars['object']->getID();

$object = $vars['object'];

$title = $object->data['title'];
if (!empty($object->data['og']['og:title']))
    $title = $object->data['og']['og:title'];

$description = $object->data['description'];
if (!empty($object->data['og']['og:description']))
    $description = $object->data['og']['og:description'];

$image = "";
if (!empty($object->data['og']['og:image']))
    $image = $object->data['og']['og:image'];

?>
<div class="row unfurled-url" id="<?= $vars['id']; ?>" data-url="<?= htmlentities($object->source_url, ENT_QUOTES, 'UTF-8'); ?>">
    
    <h2><a href="<?= htmlentities($object->source_url, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></a></h2>
    <p class="description"><?= htmlentities($description, ENT_QUOTES, 'UTF-8'); ?></p>
        
    <?php
    // Load oembed html if ok
    if (!empty($object->data['oembed']['json']) && $object->isOEmbedWhitelisted()) {
        
        $url = $object->data['oembed']['json'][0];
        ?>
    <div class="oembed" data-url="<?= html_entities($url); ?>"></div>
        <?php
    } else if (!empty($image)) {
        ?>
    <div class="image"><a href="<?= htmlentities($object->source_url, ENT_QUOTES, 'UTF-8'); ?>"><img src="<?= htmlentities($image); ?>"/></a></div>
    <?php
    }
    ?>
</div>