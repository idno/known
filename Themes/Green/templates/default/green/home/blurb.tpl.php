<?php

    // Display the main user's profile block when the offset = 0 (i.e. we're on the main homepage)

    if ($vars['offset'] == 0) {
        if ($user = \Idno\Entities\User::getOne()) {
            $t = $this;
            echo $t->__(['user' => $user])->draw('entity/User');
        }
    }