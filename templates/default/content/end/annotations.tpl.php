<?php

    if ($replies = $vars['object']->getAnnotations('reply')) {
        echo $this->__(array('annotations' => $replies))->draw('entity/annotations/replies');
    }
    if ($likes = $vars['object']->getAnnotations('like')) {
        echo $this->__(array('annotations' => $likes))->draw('entity/annotations/likes');
    }
    if ($shares = $vars['object']->getAnnotations('share')) {
        echo $this->__(array('annotations' => $shares))->draw('entity/annotations/shares');
    }
    if ($rsvps = $vars['object']->getAnnotations('rsvp')) {
        echo $this->__(array('annotations' => $rsvps))->draw('entity/annotations/rsvps');
    }
    if ($mentions = $vars['object']->getAnnotations('mention')) {
        echo $this->__(array('annotations' => $mentions))->draw('entity/annotations/mentions');
    }
