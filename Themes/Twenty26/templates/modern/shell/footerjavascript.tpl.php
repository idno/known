<script src="<?= \Idno\Core\Idno::site()->config()->getStaticURL() ?>Themes/Twenty26/dist/modern.min.js" defer></script>
<?php
// Render any plugin-registered JS assets
if ($assets = \Idno\Core\Idno::site()->currentPage()->getAssets('javascript')) {
    foreach ($assets as $asset) {
        echo '<script src="' . htmlspecialchars($asset) . '"></script>' . "\n";
    }
}
?>
