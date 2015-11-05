<?php

    header("Content-type: application/json");

?>
{
"name": "<?=htmlspecialchars(\Idno\Core\Idno::site()->config()->getTitle())?>",
"short_name": "<?=htmlspecialchars(\Idno\Core\Idno::site()->config()->getTitle())?>",
"icons": [
{
"src": "<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/logos/logo_k_36.png",
"sizes": "36x36",
"type": "image/png"
},
{
"src": "<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/logos/logo_k_48.png",
"sizes": "48x48",
"type": "image/png"
},
{
"src": "<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/logos/logo_k_72.png",
"sizes": "72x72",
"type": "image/png"
},
{
"src": "<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/logos/logo_k_96.png",
"sizes": "96x96",
"type": "image/png"
},
{
"src": "<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/logos/logo_k_144.png",
"sizes": "144x144",
"type": "image/png"
},
{
"src": "<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/logos/logo_k_192.png",
"sizes": "192x192",
"type": "image/png"
}
],
"start_url": "../",
"display": "standalone"
}
