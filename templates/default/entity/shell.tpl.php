<?php

    if (is_array($vars['object'])) {
        foreach($vars['object'] as $object) {
            echo $object->draw();
        }
    }

    //if (!empty($vars['object'])) echo $vars['object']->draw();