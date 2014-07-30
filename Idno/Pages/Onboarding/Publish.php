<?php

    /**
     * User profile editing for onboarding
     */

    namespace Idno\Pages\Onboarding {

        class Publish extends \Idno\Common\Page
        {

            function getContent()
            {

                $this->gatekeeper();

                $user = \Idno\Core\site()->session()->currentUser();

                $t = \Idno\Core\site()->template();
                echo $t->__(array(

                    'title' => "Publish your first story!",
                    'body'  => $t->__([
                            'user'         => $user,
                            'contentTypes' => \Idno\Common\ContentType::getRegistered()
                        ])->draw('onboarding/publish'),

                ))->drawPage();

            }

        }

    }