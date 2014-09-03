<?php

    if (\Idno\Core\site()->canWrite()) {
        echo $this->draw('content/create');
    }

    // This template may be obsolete