<?php

    namespace IdnoPlugins\FooterJS {

        class Main extends \Idno\Common\Plugin {
            function registerPages() {
                // Administration page
                \Idno\Core\Idno::site()->addPageHandler('admin/footerjs','\IdnoPlugins\FooterJS\Pages\Admin');

                \Idno\Core\Idno::site()->template()->extendTemplate('shell/footer','footerjs/footer');
                \Idno\Core\Idno::site()->template()->extendTemplate('shell/head','footerjs/header');
                \Idno\Core\Idno::site()->template()->extendTemplate('admin/menu/items','admin/footerjs/menu');
            }
        }

    }