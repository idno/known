<?php

    /**
     * Site administration
     *
     * @package idno
     * @subpackage core
     */

namespace Idno\Core {

    class Admin extends \Idno\Common\Component
    {

        function registerPages()
        {
            site()->routes()->addRoute('/admin/?', '\Idno\Pages\Admin\Home');
            site()->routes()->addRoute('/admin/plugins/?', '\Idno\Pages\Admin\Plugins');
            site()->routes()->addRoute('/admin/themes/?', '\Idno\Pages\Admin\Themes');
            site()->routes()->addRoute('/admin/dependencies/?', '\Idno\Pages\Admin\Dependencies');
            site()->routes()->addRoute('/admin/homepage/?', '\Idno\Pages\Admin\Homepage');
            site()->routes()->addRoute('/admin/email/?', '\Idno\Pages\Admin\Email');
            site()->routes()->addRoute('/admin/emailtest/?', '\Idno\Pages\Admin\EmailTest');
            site()->routes()->addRoute('/admin/about/?', '\Idno\Pages\Admin\About');
            site()->routes()->addRoute('/admin/users/?', '\Idno\Pages\Admin\Users');
            site()->routes()->addRoute('/admin/export/?', '\Idno\Pages\Admin\Export');
            site()->routes()->addRoute('/admin/export/generate/?', '\Idno\Pages\Admin\Export\Generate');
            //site()->routes()->addRoute('/admin/export/download/?', '\Idno\Pages\Admin\Export\Download');
            site()->routes()->addRoute('/admin/export/rss/?', '\Idno\Pages\Admin\Export\RSS');
            site()->routes()->addRoute('/admin/import/?', '\Idno\Pages\Admin\Import');
            site()->routes()->addRoute('/admin/diagnostics/?', '\Idno\Pages\Admin\Diagnostics');
            site()->routes()->addRoute('/admin/statistics/?', '\Idno\Pages\Admin\Statistics');

            if (!empty(\Idno\Core\Idno::site()->config()->capture_logs) && \Idno\Core\Idno::site()->config()->capture_logs) {
                site()->routes()->addRoute('/admin/logs/?', '\Idno\Pages\Admin\Logs');
            }

            // Override the page shell
            site()->template()->addUrlShellOverride('admin', 'settings-shell');
        }

        /**
         * Retrieve users by admins.
         * @param type $limit
         * @param type $offset
         * @return type
         */
        static function getAdmins($limit = 10, $offset = 0)
        {
            return \Idno\Entities\User::get(['admin' => true], [], $limit, $offset);
        }

    }

}

