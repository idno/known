<?php

namespace Idno\Pages\Admin\Export {

    use Idno\Common\Page;
    use Idno\Core\Migration;

    class RSS extends Page
    {

        function postContent()
        {

            $this->adminGatekeeper();

            set_time_limit(0);

            $hide_private = true;
            if ($private = $this->getInput('allposts')) {
                $hide_private = false;
            }

            $f = Migration::getExportRSS($hide_private);

            if ($f) {
                $stats = fstat($f);

                header('Content-type: text/rss');
                header('Content-disposition: attachment; filename=export.rss');
                header('Content-Length: ' . $stats['size']);

                while ($content = fgets($f)) {
                    echo $content;
                }

                fclose($f);
            } else {
                \Idno\Core\Idno::site()->session()->addMessage(
                    \Idno\Core\Idno::site()->language()->_('There was a problem generating the export. Please try again later.'),
                    'alert-danger'
                );
                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/');
            }
            exit;

        }

    }

}

