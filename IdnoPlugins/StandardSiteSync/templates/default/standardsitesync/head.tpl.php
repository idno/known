<?php

if (!empty($vars['object']) && $vars['object'] instanceof \Idno\Common\Entity) {
    $owner = $vars['object']->getOwner();
    if ($owner && $owner instanceof \Idno\Entities\User) {
        $session = \IdnoPlugins\StandardSiteSync\Main::getATProtoSessionForUser($owner);
        if (!empty($session['did'])) {
            $rkey = (string) $vars['object']->getID();
            $atUri = 'at://' . $session['did'] . '/site.standard.document/' . $rkey;
            ?>
    <link rel="site.standard.document" href="<?php echo htmlspecialchars($atUri) ?>" />
            <?php
        }
    }
}
