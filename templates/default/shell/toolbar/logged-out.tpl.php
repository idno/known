<li><a href="/session/login">Sign in</a></li>
<?php

    if (\Idno\Core\site()->config()->open_registration == true) {

?>
<li><a href="/account/register">Register</a></li>
<?php

    }

?>