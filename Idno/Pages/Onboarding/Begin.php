<?php

    /**
     * Index for onboarding
     */

    namespace Idno\Pages\Onboarding {

        class Begin extends \Idno\Common\Page
        {

            function getContent()
            {

                $t = \Idno\Core\site()->template();
                echo $t->__([
                    'body' => $t->draw('onboarding/begin'),
                    'title' => 'Welcome to Known'
                ])->draw('shell/simple');

            }

            function postContent()
            {

            }

        }

    }