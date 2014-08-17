<li><a href="<?= \Idno\Core\site()->config()->url ?>session/login">Sign in</a></li>
<?php

    if (\Idno\Core\site()->config()->open_registration == true) {

?>
<li><a href="<?= \Idno\Core\site()->config()->url ?>account/register">Register</a></li>
<?php

    }

?>
<?=$this->draw('shell/toolbar/logged-out/items')?>