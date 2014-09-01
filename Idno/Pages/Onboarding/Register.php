<?php

    /**
     * User registration for onboarding
     */

    namespace Idno\Pages\Onboarding {

        class Register extends \Idno\Common\Page
        {

            function getContent()
            {

                $set_name = $this->getInput('set_name');
                if (!empty($set_name)) {
                    $_SESSION['set_name'] = $set_name;
                }

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