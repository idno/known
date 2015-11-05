<?php

    if (\Idno\Core\Idno::site()->canWrite()) {
        echo $this->draw('content/create');
    }

    // This template may be obsolete