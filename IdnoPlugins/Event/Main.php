<?php

    namespace IdnoPlugins\Event {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {

                // Events
                \Idno\Core\site()->addPageHandler('/event/edit/?', '\IdnoPlugins\Event\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/event/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/event/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\Delete');
                \Idno\Core\site()->addPageHandler('/event/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');

                // RSVPs
                \Idno\Core\site()->addPageHandler('/rsvp/edit/?', '\IdnoPlugins\Event\Pages\RSVP\Edit');
                \Idno\Core\site()->addPageHandler('/rsvp/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\RSVP\Edit');
                \Idno\Core\site()->addPageHandler('/rsvp/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Event\Pages\RSVP\Delete');
                \Idno\Core\site()->addPageHandler('/rsvp/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');

            }

            function registerContentTypes() {

                \Idno\Common\ContentType::register('\IdnoPlugins\Event\ContentType');
                \Idno\Common\ContentType::register('\IdnoPlugins\Event\RSVPContentType');

            }
        }

    }