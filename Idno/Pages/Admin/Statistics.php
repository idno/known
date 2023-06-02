<?php

namespace Idno\Pages\Admin {

    class Statistics extends \Idno\Common\Page
    {

        function getContent()
        {
            $this->adminGatekeeper(); // Admins only
            $report = $this->getInput('report');

            $stats = \Idno\Core\Statistics::gather($report);

            if (empty($stats) || !is_array($stats)) {
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_("Something went wrong, either no statistics were returned or it wasn't in the correct format."));
            }

            if ($this->xhr) {

                header('Content-type: application/json');

                echo json_encode($stats);

            } else {
                $t = \Idno\Core\Idno::site()->template();
                $t->body = $t->__(['statistics' => $stats, 'tab' => $this->getInput('tab', $report)])->draw('admin/statistics');
                $t->title = \Idno\Core\Idno::site()->language()->_('Statistics');
                $t->drawPage();
            }
        }

    }

}
