<?php

namespace Idno\Pages\Account\Export {

    use Idno\Common\Page;
    use Idno\Core\Idno;
    use Idno\Core\Migration;
    use Symfony\Component\HttpFoundation\HeaderUtils;


    class RSS extends Page
    {

        function postContent()
        {

            $this->gatekeeper();

            set_time_limit(0);

            \Idno\Core\Idno::site()->response()->headers->set('Content-type', 'text/rss');
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                'user_export.rss'
            );
            
            \Idno\Core\Idno::site()->response()->headers->set('Content-Disposition', $disposition);

            $hide_private = true;
            if ($private = $this->getInput('allposts')) {
                $hide_private = false;
            }

            if ($f = Migration::getExportRSS($hide_private, Idno::site()->session()->currentUserUUID())) {

                $stats = fstat($f);

                \Idno\Core\Idno::site()->response()->headers->set('Content-Length:', $stats['size']);
                $file = '';
                while ($content = fgets($f)) {
                     $file.=$content;
                }
                fclose($f);
                \Idno\Core\Idno::site()->response()->setContent($file);
            }
            exit;

        }

    }

}

