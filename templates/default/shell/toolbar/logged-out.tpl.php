<?php

    echo $this->draw('shell/toolbar/login');
    if (\Idno\Core\site()->config()->open_registration == true && \Idno\Core\site()->config()->canAddUsers()) {

        echo $this->draw('shell/toolbar/register');

    }

    echo $this->draw('shell/toolbar/logged-out/items');