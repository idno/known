<?php

    namespace knownPlugins\Event {

        class Main extends \known\Common\Plugin {
            function registerPages() {

                // Events
                \known\Core\site()->addPageHandler('/event/edit/?', '\knownPlugins\Event\Pages\Edit');
                \known\Core\site()->addPageHandler('/event/edit/([A-Za-z0-9]+)/?', '\knownPlugins\Event\Pages\Edit');
                \known\Core\site()->addPageHandler('/event/delete/([A-Za-z0-9]+)/?', '\knownPlugins\Event\Pages\Delete');
                \known\Core\site()->addPageHandler('/event/([A-Za-z0-9]+)/.*', '\known\Pages\Entity\View');

                // RSVPs
                \known\Core\site()->addPageHandler('/rsvp/edit/?', '\knownPlugins\Event\Pages\RSVP\Edit');
                \known\Core\site()->addPageHandler('/rsvp/edit/([A-Za-z0-9]+)/?', '\knownPlugins\Event\Pages\RSVP\Edit');
                \known\Core\site()->addPageHandler('/rsvp/delete/([A-Za-z0-9]+)/?', '\knownPlugins\Event\Pages\RSVP\Delete');
                \known\Core\site()->addPageHandler('/rsvp/([A-Za-z0-9]+)/.*', '\known\Pages\Entity\View');

            }

            function registerContentTypes() {

                \known\Common\ContentType::register('\knownPlugins\Event\ContentType');
                \known\Common\ContentType::register('\knownPlugins\Event\RSVPContentType');

            }
        }

    }