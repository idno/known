<?php

    if (!empty($vars['contentTypes'])) {

        echo $this->draw('content/create');

    } else {

?>

        <h1>
            This is idno.
        </h1>

<?php

    }

    if (!empty($feed)) {

        // TODO: draw feed of existing content that the current user can see

    }

?>