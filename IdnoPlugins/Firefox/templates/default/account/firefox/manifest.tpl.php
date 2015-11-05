<?php

    $icons = Idno\Core\site()->getSiteIcons();
    
?>
    "name": "<?=htmlspecialchars(\Idno\Core\Idno::site()->config()->title)?>",
    "iconURL": "<?=$icons['defaults']['default_16']; ?>",
    "icon32URL": "<?=$icons['defaults']['default_32']; ?>",
    "icon64URL": "<?=$icons['defaults']['default_64']; ?>",

    "workerURL": "<?=\Idno\Core\Idno::site()->config()->url?>IdnoPlugins/Firefox/worker.js",
    //"sidebarURL": "<?=\Idno\Core\Idno::site()->config()->url?>firefox/sidebar",
    "shareURL": "<?=\Idno\Core\Idno::site()->config()->url?>share?share_url=%{url}&share_title=%{title}&via=ff_social",

    "description": "Powered by Known",
    "author": "Known, Inc",
    "homepageURL": "https://withknown.com/",

    "version": "0.1"
    