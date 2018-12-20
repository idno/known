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
            site()->addPageHandler('/admin/?', '\Idno\Pages\Admin\Home');
            site()->addPageHandler('/admin/plugins/?', '\Idno\Pages\Admin\Plugins');
            site()->addPageHandler('/admin/themes/?', '\Idno\Pages\Admin\Themes');
            site()->addPageHandler('/admin/dependencies/?', '\Idno\Pages\Admin\Dependencies');
            site()->addPageHandler('/admin/homepage/?', '\Idno\Pages\Admin\Homepage');
            site()->addPageHandler('/admin/email/?', '\Idno\Pages\Admin\Email');
            site()->addPageHandler('/admin/emailtest/?', '\Idno\Pages\Admin\EmailTest');
            site()->addPageHandler('/admin/about/?', '\Idno\Pages\Admin\About');
            site()->addPageHandler('/admin/users/?', '\Idno\Pages\Admin\Users');
            site()->addPageHandler('/admin/export/?', '\Idno\Pages\Admin\Export');
            site()->addPageHandler('/admin/export/generate/?', '\Idno\Pages\Admin\Export\Generate');
            //site()->addPageHandler('/admin/export/download/?', '\Idno\Pages\Admin\Export\Download');
            site()->addPageHandler('/admin/export/rss/?', '\Idno\Pages\Admin\Export\RSS');
            site()->addPageHandler('/admin/import/?', '\Idno\Pages\Admin\Import');
            site()->addPageHandler('/admin/diagnostics/?', '\Idno\Pages\Admin\Diagnostics');
            site()->addPageHandler('/admin/statistics/?', '\Idno\Pages\Admin\Statistics');

            if (!empty(\Idno\Core\Idno::site()->config()->capture_logs) && \Idno\Core\Idno::site()->config()->capture_logs) {
                site()->addPageHandler('/admin/logs/?', '\Idno\Pages\Admin\Logs');
            }
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

