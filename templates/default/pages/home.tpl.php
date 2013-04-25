<h1>
    This is idno.
</h1>

<?php

    if (!empty($vars['create'])) {

        echo $this->draw('content/create');

    }

    if (!empty($feed)) {

        // TODO: draw feed of existing content that the current user can see

    }

?>