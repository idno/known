<?php

namespace Idno\Pages\Service\Cron {

    class Dispatch extends \Idno\Common\Page {

        function getContent() {
            
            \Idno\Core\Idno::site()->template()->setTemplateType('json');
            
            \Idno\Core\Service::gatekeeper();
            
            $period = $this->arguments[0];
            
            if (empty($period)) {
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('No cron period was provided.'));
            }
            
            if (!array_key_exists($period, \Idno\Core\Cron::$events)) {
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_("'$period' is not a valid cron period"));
            }
            
            try {
                \Idno\Core\Idno::site()->triggerEvent("cron/$period");
            } catch (\Exception $e) {
                \Idno\Core\Idno::site()->session()->addErrorMessage("There was a problem executing cron/$period: " . $e->getMessage());
            }
            
            \Idno\Core\Idno::site()->template()->__([
                'period' => "cron/$period",
                'time' => time()
            ])->drawPage();
        }

    }

}