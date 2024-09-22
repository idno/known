<?php

namespace Idno\Pages\Chrome {

    use Idno\Common\Page;

    class Manifest extends Page
    {

        function getContent()
        {

            \Idno\Core\Idno::site()->response()->setContent(\Idno\Core\Idno::site()->template()->draw('chrome/manifest'));

        }

    }

}

