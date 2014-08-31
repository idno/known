<?php

    namespace IdnoPlugins\Like {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                \Idno\Core\site()->addPageHandler('/like/edit/?', '\IdnoPlugins\Like\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/like/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Like\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/like/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Like\Pages\Delete');
                \Idno\Core\site()->addPageHandler('/bookmark/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');
            }
        }

    }