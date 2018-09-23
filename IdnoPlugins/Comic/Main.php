<?php

namespace IdnoPlugins\Comic {

    class Main extends \Idno\Common\Plugin
    {

        function registerPages()
        {
            \Idno\Core\Idno::site()->addPageHandler('/comic/edit/?', '\IdnoPlugins\Comic\Pages\Edit');
            \Idno\Core\Idno::site()->addPageHandler('/comic/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Comic\Pages\Edit');
            \Idno\Core\Idno::site()->addPageHandler('/comic/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Comic\Pages\Delete');
            \Idno\Core\Idno::site()->addPageHandler('/comic/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');
        }

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'comic', dirname(__FILE__) . '/languages/'
                )
            );
        }
    }

}

