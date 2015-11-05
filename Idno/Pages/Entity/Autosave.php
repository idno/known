<?php

    /**
     * Autosave back-end handler
     */

    namespace Idno\Pages\Entity {

        use Idno\Entities\User;

        class Autosave extends \Idno\Common\Page
        {

            function post()
            {

                // If we're logged in, accept input and save it to the cache
                if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                    $user     = new User(); // Force events to be handled
                    $context  = $this->getInput('context');
                    $elements = $this->getInput('elements');
                    $value    = $this->getInput('value');
                    if (!empty($elements)) {
                        (new \Idno\Core\Autosave())->setValues($context, $elements);
                    }
                }

            }

        }

    }