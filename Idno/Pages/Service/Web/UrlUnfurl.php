<?php

namespace Idno\Pages\Service\Web {

    class UrlUnfurl extends \Idno\Common\Page {
        
        function deleteContent() {
            
            \Idno\Core\Idno::site()->template()->setTemplateType('json');
            header('Content-type: application/json');
            
            $this->xhrGatekeeper();
            $this->tokenGatekeeper();
            
            $url = trim($this->getInput('url'));

            if (empty($url))
                throw new \RuntimeException("You need to specify a working URL");

            // Try and get UnfurledURL entity
            if ($object = \Idno\Entities\UnfurledUrl::getBySourceURL($url)) {
                echo json_encode([
                    'url' => $url,
                    'status' => $object->delete()
                ]);
            } else {
                $this->noContent();
            }
        }

        function getContent() {

            \Idno\Core\Idno::site()->template()->setTemplateType('json');
            header('Content-type: application/json');
            
            //$this->gatekeeper(); // Gatekeeper to ensure this service isn't abused by third parties/ UPDATE: Needs to be accessible to logged out users, TODO, find a way to prevent abuse
            $this->xhrGatekeeper();
            $this->tokenGatekeeper();

            $url = trim($this->getInput('url'));
            $forcenew = $this->getInput('forcenew', false);
            

            if (empty($url))
                throw new \RuntimeException("You need to specify a working URL");

            // Try and get UnfurledURL entity
            $object = \Idno\Entities\UnfurledUrl::getBySourceURL($url);
            if (!$forcenew && !empty($object)) {
                $unfurled = $object->data;
                $template = new \Idno\Core\Template();
                $template->setTemplateType('default');
                $unfurled['id'] = $object->getID();
                $unfurled['rendered'] = $template->__(['object' => $object])->draw('entity/UnfurledUrl');
                
                echo json_encode($unfurled, JSON_PRETTY_PRINT);
                
                exit;
            }
            
            if (empty($object))
                $object = new \Idno\Entities\UnfurledUrl();
            $object->setAccess('PUBLIC');
            $result = $object->unfurl($url);
            
            if (!$result)
                throw new \RuntimeException("Url $url could not be unfurled");

            $object->save();
            
            // Pre-render (for javascript)
            $unfurled = $object->data;
            $template = new \Idno\Core\Template();
            $template->setTemplateType('default');
            $unfurled['id'] = $object->getID();
            $unfurled['rendered'] = $template->__(['object' => $object])->draw('entity/UnfurledUrl');

            echo json_encode($unfurled, JSON_PRETTY_PRINT);
        }

    }

}