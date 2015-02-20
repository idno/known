<?php

    namespace IdnoPlugins\Bridgy\Pages {

        use Idno\Common\Page;

        class Account extends Page
        {

            function getContent()
            {

                $t = \Idno\Core\site()->template();
                $t->body = $t->__([])->draw('bridgy/account');
                $t->title = 'Brid.gy';
                $t->drawPage();

            }

        }

    }