<li><a href="<?= \known\Core\site()->config()->url ?>session/login">Sign in</a></li>
<?php

    if (\known\Core\site()->config()->open_registration == true) {

?>
<li><a href="<?= \known\Core\site()->config()->url ?>account/register">Register</a></li>
<?php

    }

?>