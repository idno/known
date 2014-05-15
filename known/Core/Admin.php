<?php

    /**
     * Site administration
     *
     * @package known
     * @subpackage core
     */

    namespace known\Core {

        class Admin extends \known\Common\Component
        {

            function registerPages()
            {
                site()->addPageHandler('/admin/?', '\known\Pages\Admin\Home');
                site()->addPageHandler('/admin/plugins/?', '\known\Pages\Admin\Plugins');
                site()->addPageHandler('/admin/dependencies/?', '\known\Pages\Admin\Dependencies');
                site()->addPageHandler('/admin/about/?', '\known\Pages\Admin\About');
            }

        }

    }