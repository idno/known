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

    if (!empty($vars['feed'])) {

        foreach($vars['feed'] as $entry) {
            echo '.';
        }

    }

?>