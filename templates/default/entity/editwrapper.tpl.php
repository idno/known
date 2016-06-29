<?php

    if (!empty($vars['body'])) {
        echo $vars['body'];
    } else {
        echo $vars['object']->drawEdit();
    }