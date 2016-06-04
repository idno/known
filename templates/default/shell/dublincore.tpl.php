<?php

    $currentPage = \Idno\Core\Idno::site()->currentPage();

?>
<!-- Dublin Core -->
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/">
<meta name="DC.title" content="<?= htmlspecialchars($vars['title']) ?>">
<meta name="DC.description" content="<?= htmlspecialchars($vars['description']) ?>"><?php

    if ($currentPage->isPermalink()) {
        /* @var \Idno\Common\Entity $object */
        if ($vars['object'] instanceof \Idno\Common\Entity) {
            $object = $vars['object'];
            if ($creator = $object->getOwner()) {
                ?>
                <meta name="DC.creator" content="<?= htmlentities($creator->getTitle()) ?>"><?php
            }
            if ($created = $object->created) {
                ?>
                <meta name="DC.date" content="<?= date('c', $created) ?>"><?php
            }
            if ($url = $object->getDisplayURL()) {
                ?>
                <meta name="DC.identifier" content="<?= htmlspecialchars($url) ?>"><?php
            }

        }
    }

?>