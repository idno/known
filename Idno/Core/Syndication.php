<?php

    /**
     * Syndication (or POSSE - Publish Own Site, Share Everywhere) helpers
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        class Syndication extends \Idno\Common\Component
        {

            public $services = array();
            public $checkers = array(); // Our array of "does user X have service Y enabled?" checkers

            function init()
            {
            }

            function registerEventHooks()
            {
                \Idno\Core\site()->events()->addListener('syndicate', function (\Idno\Core\Event $event) {

                    if (!empty($event->data()['object'])) {
                        $content_type = $event->data()['object']->getActivityStreamsObjectType();
                        if ($services = \Idno\Core\site()->syndication()->getServices($content_type)) {
                            if ($selected_services = \Idno\Core\site()->currentPage()->getInput('syndication')) {
                                if (!empty($selected_services) && is_array($selected_services)) {
                                    foreach ($selected_services as $selected_service) {
                                        if (in_array($selected_service, $services)) {
                                            \Idno\Core\site()->events()->dispatch('post/' . $content_type . '/' . $selected_service, $event);
                                        }
                                    }
                                }
                            }
                        }
                    }

                });
            }

            /**
             * Register syndication $service with idno.
             * @param string $service The name of the service.
             * @param callable $checker A function that will return true if the current user has the service enabled; false otherwise
             * @param array $content_types An array of content types that the service supports syndication for
             */
            function registerService($service, callable $checker, $content_types = ['article', 'note', 'event', 'rsvp', 'reply'])
            {
                $service = strtolower($service);
                if (!empty($content_types)) {
                    foreach ($content_types as $content_type) {
                        $this->services[$content_type][] = $service;
                    }
                }
                $this->checkers[$service] = $checker;
                \Idno\Core\site()->template()->extendTemplate('content/syndication', 'content/syndication/' . $service);
            }

            /**
             * Adds a content type that the specified service will support
             * @param $service
             * @param $content_type
             */
            function addServiceContentType($service, $content_type)
            {
                if (!empty($this->services[$content_type]) && !in_array($service, $this->services[$content_type])) {
                    $this->services[$content_type][] = $service;
                }
            }

            /**
             * Return an array of the services registered for a particular content type
             * @param $content_type
             * @return array
             */
            function getServices($content_type = false)
            {
                if (!empty($content_type)) {
                    if (!empty($this->services[$content_type])) {
                        return $this->services[$content_type];
                    }
                } else {
                    $return = [];
                    if (!empty($this->services)) {
                        foreach($this->services as $service) {
                            $return = array_merge($return, $service);
                        }
                    }
                    return array_unique($return);
                }

                return [];
            }

            //function triggerSyndication

            /**
             * Does the currently logged-in user have service $service?
             * @param $service
             * @return bool
             */
            function has($service)
            {
                if (!array_key_exists($service, $this->checkers)) {
                    return false;
                }
                $checker = $this->checkers[$service];

                return $checker();
            }

        }

    }
