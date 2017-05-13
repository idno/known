<?php

    foreach (['reply'   => 'replies',
              'like'    => 'likes',
              'share'   => 'shares',
              'rsvp'    => 'rsvps',
              'mention' => 'mentions'] as $annotationType => $templateName) {

        if ($annotations = $vars['object']->getAnnotations($annotationType)) {
            echo $this->__(array('annotations' => $annotations))->draw('entity/annotations/' . $templateName);
        }

    }

