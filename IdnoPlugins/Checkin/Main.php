<?php

namespace IdnoPlugins\Checkin {

    class Main extends \Idno\Common\Plugin
    {
        function registerPages()
        {
            \Idno\Core\Idno::site()->routes()->addRoute('/checkin/edit/?', '\IdnoPlugins\Checkin\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/checkin/edit/:id/?', '\IdnoPlugins\Checkin\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/checkin/delete/:id/?', '\IdnoPlugins\Checkin\Pages\Delete');

            \Idno\Core\Idno::site()->template()->extendTemplate('shell/head', 'checkin/head');
        }

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'checkin', dirname(__FILE__) . '/languages/'
                )
            );
        }
    }

}

