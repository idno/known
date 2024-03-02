<?php

    /**
     * Site administration
     *
     * @package    idno
     * @subpackage core
     */

namespace Idno\Core {

    class Admin extends \Idno\Common\Component
    {

        function registerPages()
        {
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/?', '\Idno\Pages\Admin\Home');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/plugins/?', '\Idno\Pages\Admin\Plugins');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/themes/?', '\Idno\Pages\Admin\Themes');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/dependencies/?', '\Idno\Pages\Admin\Dependencies');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/homepage/?', '\Idno\Pages\Admin\Homepage');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/email/?', '\Idno\Pages\Admin\Email');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/emailtest/?', '\Idno\Pages\Admin\EmailTest');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/about/?', '\Idno\Pages\Admin\About');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/users/?', '\Idno\Pages\Admin\Users');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/export/?', '\Idno\Pages\Admin\Export');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/export/generate/?', '\Idno\Pages\Admin\Export\Generate');
            //\Idno\Core\Idno::site()->routes()->addRoute('/admin/export/download/?', '\Idno\Pages\Admin\Export\Download');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/export/rss/?', '\Idno\Pages\Admin\Export\RSS');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/import/?', '\Idno\Pages\Admin\Import');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/diagnostics/?', '\Idno\Pages\Admin\Diagnostics');
            \Idno\Core\Idno::site()->routes()->addRoute('/admin/statistics/?', '\Idno\Pages\Admin\Statistics');

            if (!empty(\Idno\Core\Idno::site()->config()->capture_logs) && \Idno\Core\Idno::site()->config()->capture_logs) {
                \Idno\Core\Idno::site()->routes()->addRoute('/admin/logs/?', '\Idno\Pages\Admin\Logs');
            }

            // Override the page shell
            \Idno\Core\Idno::site()->template()->addUrlShellOverride('admin', 'settings-shell');
        }

        /**
         * Retrieve users by admins.
         *
         * @param  int $limit
         * @param  int $offset
         * @return array
         */
        static function getAdmins($limit = 10, $offset = 0)
        {
            return \Idno\Entities\User::get(['admin' => true], [], $limit, $offset);
        }

    }

}

