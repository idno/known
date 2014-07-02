<?php

    namespace IdnoPlugins\Media {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                \Idno\Core\site()->addPageHandler('/media/edit/?', '\IdnoPlugins\Media\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/media/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Media\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/media/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Media\Pages\Delete');
                \Idno\Core\site()->template()->extendTemplate('shell/footer','media/shell/footer');
            }
        }

    }