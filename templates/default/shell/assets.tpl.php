<?php

    if (!empty(\Idno\Core\Idno::site()->config()->assets)) {
        foreach (\Idno\Core\Idno::site()->config()->assets as $asset => $enabled) {
            if (!empty($enabled)) {
                echo $this->draw('assets/' . $asset);
            }
        }
    }