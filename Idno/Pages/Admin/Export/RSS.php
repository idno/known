<?php

    namespace Idno\Pages\Admin\Export {

        use Idno\Common\Page;
        use Idno\Core\Migration;

        class RSS extends Page {

            function getContent() {

                set_time_limit(0);

                header('Content-type: text/rss');
                header('Content-disposition: attachment; filename=export.rss');

                echo Migration::getExportRSS(true);

            }

        }

    }