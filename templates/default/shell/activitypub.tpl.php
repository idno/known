<?php

$currentPage = \Idno\Core\Idno::site()->currentPage();
$pageOwner = $currentPage->getOwner();

if (!empty($vars['user']) && $vars['user'] instanceof Idno\Entities\User) {
    ?>
    <link rel="alternate" type="application/activity+json" href="<?php echo $vars['user']->getActorID() ?>" />
    <?php
} elseif (!empty($vars['object'])) {
}
