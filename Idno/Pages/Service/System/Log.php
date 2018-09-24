<?php

namespace Idno\Pages\Service\System {

    class Log extends \Idno\Common\Page
    {

        function postContent()
        {

            $this->xhrGatekeeper();

            $message = $this->getInput('message');
            $level = $this->getInput('level', 'INFO');

            $message = 'Client Runtime: ' . $message;

            $stats = \Idno\Core\Idno::site()->statistics();

            switch (strtoupper($level)) {

                case 'ALERT' :
                case 'EXCEPTION':
                case 'ERROR':
                    \Idno\Core\Idno::site()->logging()->error($message);

                    // Lets log an explicit stat
                    if (!empty($stats)) {
                        $stats->increment("javascript.error");
                    }

                    // Since this is likely to be unattended, we should also log an oops.
                    try {
                        \Idno\Core\Logging::oopsAlert($message, 'Javascript Error');
                    } catch (Exception $ex) {
                        error_log($ex->getMessage());
                    }
                    break;

                case 'WARN':
                case 'WARNING':
                    \Idno\Core\Idno::site()->logging()->warning($message);

                    // Lets log an explicit stat
                    if (!empty($stats)) {
                        $stats->increment("javascript.warning");
                    }

                    break;

                default:

                    // Lets log an explicit stat
                    if (!empty($stats)) {
                        $stats->increment("javascript.info");
                    }

                    \Idno\Core\Idno::site()->logging()->info($message);
            }

        }

    }

}
