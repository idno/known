<?php

    namespace Idno\Pages\Chrome {

        use Idno\Common\Page;

        class Manifest extends Page
        {

            function getContent()
            {

                echo \Idno\Core\Idno::site()->template()->draw('chrome/manifest');

            }

        }

    }