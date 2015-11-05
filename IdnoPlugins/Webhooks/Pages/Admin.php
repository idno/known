<?php

    namespace IdnoPlugins\Webhooks\Pages
    {

        use Idno\Common\Page;

        class Admin extends Page {

            function getContent() {

                $this->adminGatekeeper();
                $t = \Idno\Core\Idno::site()->template();
                $body = $t->draw('webhooks/admin/home');
                $t->__(array('title' => 'Webhooks', 'body' => $body))->drawPage();

            }

            function postContent() {

                $this->adminGatekeeper();
                $hooks = $this->getInput('webhooks');
                $titles = $this->getInput('titles');
                $webhook_syndication = array();
                if (is_array($hooks) && !empty($hooks)) {
                    foreach($hooks as $key => $hook) {

                        $hook = trim($hook);
                        if (!empty($hook)) {
                            if (filter_var($hook, FILTER_VALIDATE_URL)) {
                                if (!empty($titles[$key])) {
                                    $title = $titles[$key];
                                } else {
                                    $title = parse_url($hook, PHP_URL_HOST);
                                }
                                $webhook_syndication[] = array('url' => $hook, 'title' => $title);
                            } else {
                                \Idno\Core\Idno::site()->session()->addErrorMessage($hook . " doesn't seem to be a valid URL.");
                            }
                        }
                    }
                }
                \Idno\Core\Idno::site()->config->webhook_syndication = $webhook_syndication;
                \Idno\Core\Idno::site()->config->save();
                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/webhooks/');

            }

        }

    }