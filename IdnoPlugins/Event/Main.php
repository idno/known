<?php

namespace IdnoPlugins\Event {

    class Main extends \Idno\Common\Plugin
    {
        function registerPages()
        {

            // Events
            \Idno\Core\Idno::site()->routes()->addRoute('/event/edit/?', '\IdnoPlugins\Event\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/event/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/event/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\Delete');
            \Idno\Core\Idno::site()->routes()->addRoute('/event/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');

            // RSVPs
            \Idno\Core\Idno::site()->routes()->addRoute('/rsvp/edit/?', '\IdnoPlugins\Event\Pages\RSVP\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/rsvp/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\RSVP\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/rsvp/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\RSVP\Delete');
            \Idno\Core\Idno::site()->routes()->addRoute('/rsvp/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');

        }

        function registerContentTypes()
        {

            \Idno\Common\ContentType::register('\IdnoPlugins\Event\ContentType');
            \Idno\Common\ContentType::register('\IdnoPlugins\Event\RSVPContentType');

        }

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'event', dirname(__FILE__) . '/languages/'
                )
            );
        }
    }

}

