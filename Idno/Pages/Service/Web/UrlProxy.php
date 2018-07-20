<?php

namespace Idno\Pages\Service\Web {

    class UrlProxy extends \Idno\Common\Page {
        
        public function getContent() {
            
            $url = trim($this->getInput('url'));
            
            if (empty($url))
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_("You need to specify a working URL"));
            
            $url = \Idno\Core\Idno::site()->triggerEvent('url/proxy', [
                'url' => $url
            ], $url);
            
            $this->forward($url);
        }
        
    }

}