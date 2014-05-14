<?php

    /**
     * Autosave back-end handler
     */

    namespace known\Pages\Entity {

        use known\Entities\User;

        class Autosave extends \known\Common\Page
        {

            function post()
            {

                // If we're logged in, accept input and save it to the cache
                if (\known\Core\site()->session()->isLoggedOn()) {
                    $user = new User(); // Force events to be handled
                    $context = $this->getInput('context');
                    $elements = $this->getInput('elements');
                    $value   = $this->getInput('value');
                    if (!empty($elements)) {
                        (new \known\Core\Autosave())->setValues($context, $elements);
                    }
                }

            }

        }

    }