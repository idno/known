<?php

namespace IdnoPlugins\Text {

    class Main extends \Idno\Common\Plugin
    {

        function registerPages()
        {
            \Idno\Core\Idno::site()->routes()->addRoute('/entry/edit/?', '\IdnoPlugins\Text\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/entry/edit/:id/?', '\IdnoPlugins\Text\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/entry/delete/:id/?', '\IdnoPlugins\Text\Pages\Delete');
            \Idno\Core\Idno::site()->routes()->addRoute('/entry/:id/.*', '\Idno\Pages\Entity\View');
        }

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'text', dirname(__FILE__) . '/languages/'
                )
            );
        }

    }

}
