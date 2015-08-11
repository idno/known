<?php

    namespace Idno\Pages\Members {

        use Idno\Common\Page;

        class Directory extends Page
        {

            function getContent()
            {

                $this->gatekeeper();
                if (!\Idno\Core\site()->config()->show_directory) {
                    $this->deniedContent();
                }

                $offset = $this->getInput('offset', 0);
                $query  = $this->getInput('q', '');

                if (!empty($query)) {
                    $search = \Idno\Core\site()->db()->createSearchArray($query);
                } else {
                    $search = [];
                }

                $users = \Idno\Entities\User::get($search, [], 20, $offset);
                $count = \Idno\Entities\User::count($search);

                $t        = \Idno\Core\site()->template();
                $t->body  = $t->__(array(
                    'users'  => $users,
                    'count'  => $count,
                    'offset' => $offset,
                    'query'  => $query
                ))->draw('members/directory');
                $t->title = 'Member Directory';
                $t->drawPage();

            }

        }

    }