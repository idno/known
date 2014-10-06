<?php

    namespace Idno\Pages\Robot {

        class Remove extends \Idno\Common\Page
        {

            function getContent()
            {
            }

            function postContent()
            {

                $this->gatekeeper();
                $user = \Idno\Core\site()->session()->currentUser();
                $user->robot_state = 0;
                $user->save();
                \Idno\Core\site()->session()->refreshSessionUser($user);
                $this->forward($_SERVER['HTTP_REFERER']);

            }

        }

    }