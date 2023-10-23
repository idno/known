<?php

namespace IdnoPlugins\Event {

    class Main extends \Idno\Common\Plugin
    {
        function registerPages()
        {

            // Events
            \Idno\Core\Idno::site()->routes()->addRoute('/event/edit/?', '\IdnoPlugins\Event\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/event/edit/:id/?', '\IdnoPlugins\Event\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/event/delete/:id/?', '\IdnoPlugins\Event\Pages\Delete');
            \Idno\Core\Idno::site()->routes()->addRoute('/event/:id/.*', '\Idno\Pages\Entity\View');

            // RSVPs
            \Idno\Core\Idno::site()->routes()->addRoute('/rsvp/edit/?', '\IdnoPlugins\Event\Pages\RSVP\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/rsvp/edit/:id/?', '\IdnoPlugins\Event\Pages\RSVP\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/rsvp/delete/:id/?', '\IdnoPlugins\Event\Pages\RSVP\Delete');
            \Idno\Core\Idno::site()->routes()->addRoute('/rsvp/:id/.*', '\Idno\Pages\Entity\View');

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

