<?php

    namespace Idno\Core {

        class HelperRobot extends \Idno\Common\Component
        {

            function init()
            {
                if (site()->session()->isLoggedOn()) {
                    if (!empty(site()->session()->currentUser()->robot_status)) {
                        $this->registerEvents();
                    }
                }
            }

            function registerEvents()
            {

                \Idno\Core\site()->addEventHook('syndicate', function (\Idno\Core\Event $event) {

                    if ($object = $event->data()['object']) {
                        if (site()->session()->isLoggedOn()) {
                            if (!empty(site()->session()->currentUser()->robot_status)) {

                            }
                        }
                    }

                });

            }

        }

    }