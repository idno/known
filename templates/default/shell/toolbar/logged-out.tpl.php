<li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>session/login">Sign in</a></li>
<?php

    if (\Idno\Core\site()->config()->open_registration == true && \Idno\Core\site()->config()->canAddUsers()) {

?>
<li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/register">Register</a></li>
<?php

    }

?>
<?=$this->draw('shell/toolbar/logged-out/items')?>