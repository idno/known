<?php

    /**
     * Index for onboarding
     */

    namespace Idno\Pages\Onboarding {

        class Begin extends \Idno\Common\Page
        {

            function getContent()
            {

                $set_name = $this->getInput('set_name');
                if (!empty($set_name)) {
                    $_SESSION['set_name'] = $set_name;
                }

                $t = \Idno\Core\site()->template();
                echo $t->__([
                    'body' => $t->draw('onboarding/begin'),
                    'title' => 'Welcome to Known',
                    'messages' => \Idno\Core\site()->session()->getAndFlushMessages()
                ])->draw('shell/simple');

            }

            function postContent()
            {

            }

        }

    }