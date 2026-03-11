<?php
/**
 * Medium-style link preview card, rendered server-side from cached metadata.
 *
 * Expects $vars['preview'] with keys: url, title, description, image, domain, site_name
 */

if (empty($vars['preview']) || empty($vars['preview']['url'])) {
    return;
}

$preview = $vars['preview'];
$url         = htmlentities($preview['url'], ENT_QUOTES, 'UTF-8');
$title       = htmlentities($preview['title'] ?? '', ENT_QUOTES, 'UTF-8');
$description = htmlentities($preview['description'] ?? '', ENT_QUOTES, 'UTF-8');
$image       = $preview['image'] ?? '';
$domain      = htmlentities($preview['domain'] ?? parse_url($preview['url'], PHP_URL_HOST) ?? '', ENT_QUOTES, 'UTF-8');
$siteName    = htmlentities($preview['site_name'] ?? '', ENT_QUOTES, 'UTF-8');

if (empty($title) && empty($description) && empty($image)) {
    return;
}
?>
<a href="<?= $url ?>" target="_blank" rel="noopener noreferrer" class="link-preview-card">
    <?php if (!empty($image)) { ?>
        <div class="link-preview-image">
            <img src="<?= $this->getProxiedImageUrl($image) ?>" alt="" loading="lazy" />
        </div>
    <?php } ?>
    <div class="link-preview-content">
        <?php if (!empty($title)) { ?>
            <div class="link-preview-title"><?= $title ?></div>
        <?php } ?>
        <?php if (!empty($description)) { ?>
            <div class="link-preview-description"><?= $description ?></div>
        <?php } ?>
        <div class="link-preview-domain">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <?= !empty($siteName) ? $siteName : $domain ?>
        </div>
    </div>
</a>
