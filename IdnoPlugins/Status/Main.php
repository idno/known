<?php

namespace IdnoPlugins\Status {

    class Main extends \Idno\Common\Plugin
    {

        function registerPages()
        {
            \Idno\Core\Idno::site()->routes()->addRoute('/status/edit/?', '\IdnoPlugins\Status\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/status/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Status\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/reply/edit/?', '\IdnoPlugins\Status\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/reply/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Status\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/status/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Status\Pages\Delete');
            \Idno\Core\Idno::site()->routes()->addRoute('/reply/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Status\Pages\Delete');
        }

        function registerContentTypes()
        {
            parent::registerContentTypes();

            \Idno\Common\ContentType::register($this->getNamespace() . '\\RepliesContentType');
        }

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'status', dirname(__FILE__) . '/languages/'
                )
            );
        }

    }

}
