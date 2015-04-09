<?php

    namespace IdnoPlugins\Bridgy\Pages {

        use Idno\Common\Page;

        class Account extends Page
        {

            function getContent()
            {

                if (!empty($_SERVER['HTTP_REFERER']))
                if (substr_count($_SERVER['HTTP_REFERER'], 'brid.gy')) {
                    \Idno\Core\site()->session()->addMessage("Your account has been connected with brid.gy!");
                }

                $t = \Idno\Core\site()->template();
                $t->body = $t->__([])->draw('bridgy/account');
                $t->title = 'Brid.gy';
                $t->drawPage();

            }

        }

    }