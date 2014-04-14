<?php

    /**
     * Autosave back-end handler
     */

    namespace Idno\Pages\Entity {

        class Autosave extends \Idno\Common\Page
        {

            function post()
            {

                // If we're logged in, accept input and save it to the cache
                if (\Idno\Core\site()->session()->isLoggedOn()) {
                    $context = $this->getInput('context');
                    $element = $this->getInput('element');
                    $value   = $this->getInput('value');
                    $autosave = new \Idno\Core\Autosave();
                    $autosave->setValue($context, $element, $value);
                }

            }

        }

    }