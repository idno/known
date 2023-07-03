<?php

    $hidenav = \Idno\Core\Idno::site()->currentPage()->getInput('hidenav', false);

if (empty($vars['hidenav']) && empty($hidenav)) {
    echo $this->draw('shell/toolbar/main');
} // End hidenav test

    echo $this->draw('shell/beforecontainer');
