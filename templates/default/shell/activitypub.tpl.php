<?php

$currentPage = \Idno\Core\Idno::site()->currentPage();
$pageOwner = $currentPage->getOwner();
$alt_link = '';

if (!empty($vars['user']) && $vars['user'] instanceof Idno\Entities\User) {
    $alt_link = $vars['user']->getActivityPubActorID();
} elseif (!empty($vars['object'])) {
    $alt_link = $vars['object']->getUUID();
}

if (!empty($alt_link)) {
    ?>
    <link rel="alternate" type="application/activity+json" href="<?php echo $alt_link ?>" />
    <?php
}
