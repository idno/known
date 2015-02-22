<?php

    namespace Idno\Core {

        class HelperRobot extends \Idno\Common\Component
        {

            static $changed_state = 0;

            function init()
            {
                if (site()->session()->isLoggedOn()) {
                    if (!empty(site()->session()->currentUser()->robot_state)) {
                        $this->registerEvents();
                    }
                }
                site()->addPageHandler('robot/remove/?','Idno\Pages\Robot\Remove');
            }

            function registerEvents()
            {

                \Idno\Core\site()->addEventHook('saved', function (\Idno\Core\Event $event) {

                    $eventdata = $event->data();
                    if ($object = $eventdata['object']) {
                        if (site()->session()->isLoggedOn()) {
                            if (!empty(site()->session()->currentUser()->robot_state)) {
                                $user = site()->session()->currentUser();
                                switch ($user->robot_state) {

                                    case '1':
                                        if (class_exists('IdnoPlugins\Status') && $object instanceof \IdnoPlugins\Status) {
                                            $user->robot_state = '2a';
                                        } else {
                                            $user->robot_state = '2b';
                                        }
                                        self::$changed_state = 1;
                                        break;
                                    case '2a':
                                        if (class_exists('IdnoPlugins\Photo') && $object instanceof \IdnoPlugins\Photo) {
                                            $user->robot_state = '3a';
                                        }
                                        self::$changed_state = 1;
                                        break;
                                    case '2b':
                                        $user->robot_state = '3b';
                                        self::$changed_state = 1;
                                        break;

                                }
                                $user->save();
                                site()->session()->refreshSessionUser($user);

                            }
                        }
                    }

                });

            }

        }

    }