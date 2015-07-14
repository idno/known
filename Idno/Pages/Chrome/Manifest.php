<?php

    namespace Idno\Pages\Chrome {

        use Idno\Common\Page;

        class Manifest extends Page
        {

            function getContent()
            {

                echo \Idno\Core\site()->template()->draw('chrome/manifest');

            }

        }

    }