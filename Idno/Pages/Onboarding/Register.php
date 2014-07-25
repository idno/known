<?php

    /**
     * User registration for onboarding
     */

    namespace Idno\Pages\Onboarding {

        class Register extends \Idno\Common\Page
        {

            function getContent()
            {

                $page = new \Idno\Pages\Account\Register();
                $page->setInput('onboarding',true);
                $page->getContent();

            }

            function postContent()
            {

                $page = new \Idno\Pages\Account\Register();
                $page->setInput('onboarding',true);
                $page->postContent();

            }

        }

    }