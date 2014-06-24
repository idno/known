<?php

    /**
     * User mentions
     */

    namespace Idno\Pages\Search {

        use Idno\Entities\User;

        class Mentions extends \Idno\Common\Page
        {

            function getContent()
            {

                $results  = [];
                $username = $this->getInput('username');
                if ($users = User::get([], [], 9999)) { //User::getByHandle($username)) {
                    foreach ($users as $user) {
                        /* @var \Idno\Entities\User $user */
                        $results[] = [
                            'username' => $user->getHandle(),
                            'name'     => $user->getTitle(),
                            'image'    => $user->getIcon()
                        ];
                    }
                }
                header('Content-type: text/json');
                echo json_encode($results);

            }

        }

    }