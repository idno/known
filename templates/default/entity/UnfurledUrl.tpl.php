<?php

if (empty($vars['object']) || empty($vars['object']->data)) {
    return;
}

$vars['id'] = "unfurled-url-" . $vars['object']->getID();

$object = $vars['object'];

$title = $object->source_url;
if (!empty($object->data['title'])) {
    $title = $object->data['title'];
}
if (!empty($object->data['og']['og:title'])) {
    $title = $object->data['og']['og:title'];
}

$description = "";
if (!empty($object->data['description'])) {
        $object->data['description'];
}
if (!empty($object->data['og']['og:description'])) {
    $description = $object->data['og']['og:description'];
}

$image = "";
if (!empty($object->data['og']['og:image'])) {
    $image = $object->data['og']['og:image'];
}

?>
<div class="row unfurled-url" id="<?php echo $vars['id']; ?>" data-url="<?php echo htmlentities($object->source_url, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="basics">
        <?php if (!empty($image)) { ?>
            <div class="image"><a href="<?php echo htmlentities($object->source_url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><img src="<?php echo $this->getProxiedImageUrl($image); ?>"/></a></div>
        <?php } ?>
            
            <div class="text">
                <h3><a href="<?php echo htmlentities($object->source_url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></a></h3>
                <?php if (!empty($description)) { ?><blockquote class="description"><?php echo htmlentities($description, ENT_QUOTES, 'UTF-8'); ?></blockquote><?php
                } ?>

                <!--<div class="byline"><a href="<?php echo htmlentities($object->source_url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlentities(parse_url($object->source_url, PHP_URL_HOST), ENT_QUOTES, 'UTF-8'); ?></a></div>-->
            </div>
    </div>
    
    <?php
    // Load oembed html if ok
    if (!empty($object->data['oembed']['jsonp']) && $object->isOEmbedWhitelisted()) {

        $url = $object->data['oembed']['jsonp'][0];
        ?>
    <div class="oembed" data-url="<?php echo htmlentities($url); ?>" data-format="jsonp"></div>
        <?php
    }
    if (!empty($object->data['oembed']['json']) && $object->isOEmbedWhitelisted()) {

        $url = $object->data['oembed']['json'][0];
        ?>
    <div class="oembed" data-url="<?php echo htmlentities($url); ?>" data-format="json"></div>
        <?php
    } else if (!empty($object->data['oembed']['xml']) && $object->isOEmbedWhitelisted()) {

        $url = $object->data['oembed']['xml'][0];
        ?>
    <div class="oembed" data-url="<?php echo htmlentities($url); ?>" data-format="xml"></div>
        <?php
    }?>
</div>