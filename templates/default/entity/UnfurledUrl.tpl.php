<?php
/**
 * Unfurled URL preview card (used by the JS unfurl system in edit forms
 * and as a fallback when server-side cached previews are not available).
 *
 * Uses the same visual style as the server-side LinkPreviewCard.
 */

if (empty($vars['object']) || empty($vars['object']->data)) {
    return;
}

$vars['id'] = "unfurled-url-" . $vars['object']->getID();

$object = $vars['object'];

$title = '';
if (!empty($object->data['og']['og:title'])) {
    $title = $object->data['og']['og:title'];
} elseif (!empty($object->data['title'])) {
    $title = $object->data['title'];
}

$description = '';
if (!empty($object->data['og']['og:description'])) {
    $description = $object->data['og']['og:description'];
} elseif (!empty($object->data['description'])) {
    $description = $object->data['description'];
}

$image = '';
if (!empty($object->data['og']['og:image'])) {
    $image = $object->data['og']['og:image'];
}

$domain = parse_url($object->source_url, PHP_URL_HOST) ?: '';

$siteName = '';
if (!empty($object->data['og']['og:site_name'])) {
    $siteName = $object->data['og']['og:site_name'];
}

$url = htmlentities($object->source_url, ENT_QUOTES, 'UTF-8');

if (empty($title) && empty($description) && empty($image)) {
    return;
}

?>
<div class="unfurled-url" id="<?= $vars['id'] ?>" data-url="<?= $url ?>">
    <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer" class="link-preview-card">
        <?php if (!empty($image)) { ?>
            <div class="link-preview-image">
                <img src="<?= $this->getProxiedImageUrl($image) ?>" alt="" loading="lazy" />
            </div>
        <?php } ?>
        <div class="link-preview-content">
            <?php if (!empty($title)) { ?>
                <div class="link-preview-title"><?= htmlentities($title, ENT_QUOTES, 'UTF-8') ?></div>
            <?php } ?>
            <?php if (!empty($description)) { ?>
                <div class="link-preview-description"><?= htmlentities($description, ENT_QUOTES, 'UTF-8') ?></div>
            <?php } ?>
            <div class="link-preview-domain">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                <?= htmlentities(!empty($siteName) ? $siteName : $domain, ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>
    </a>
    <?php
    // Load oembed html if ok
    if (!empty($object->data['oembed']['jsonp']) && $object->isOEmbedWhitelisted()) {
        $oembedUrl = $object->data['oembed']['jsonp'][0];
        ?>
        <div class="oembed" data-url="<?= htmlentities($oembedUrl) ?>" data-format="jsonp"></div>
    <?php
    }
    if (!empty($object->data['oembed']['json']) && $object->isOEmbedWhitelisted()) {
        $oembedUrl = $object->data['oembed']['json'][0];
        ?>
        <div class="oembed" data-url="<?= htmlentities($oembedUrl) ?>" data-format="json"></div>
    <?php
    } elseif (!empty($object->data['oembed']['xml']) && $object->isOEmbedWhitelisted()) {
        $oembedUrl = $object->data['oembed']['xml'][0];
        ?>
        <div class="oembed" data-url="<?= htmlentities($oembedUrl) ?>" data-format="xml"></div>
    <?php
    } ?>
</div>
