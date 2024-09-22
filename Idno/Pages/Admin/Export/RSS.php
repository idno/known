<?php

namespace Idno\Pages\Admin\Export {

    use Idno\Common\Page;
    use Idno\Core\Migration;
    use Symfony\Component\HttpFoundation\HeaderUtils;


    class RSS extends Page
    {

        function postContent()
        {

            $this->adminGatekeeper();

            set_time_limit(0);

            \Idno\Core\site()->response()->headers->set('Content-type', 'text/rss');
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                'export.rss'
            );

            \Idno\Core\site()->response()->headers->set('Content-Disposition', $disposition);

            $hide_private = true;
            if ($private = $this->getInput('allposts')) {
                $hide_private = false;
            }

            if ($f = Migration::getExportRSS($hide_private)) {

                $stats = fstat($f);

                \Idno\Core\site()->response()->headers->set('Content-Length:', $stats['size']);
                $file = '';
                while ($content = fgets($f)) {
                    $file .= $content;
                }

                fclose($f);
                \Idno\Core\site()->response()->setContent($file);
            }
            \Idno\Core\site()->sendResponse();

        }

    }

}

