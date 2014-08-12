<?php

    /**
     * Theme administration
     */

    namespace Idno\Pages\Admin {

        class Themes extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->__(array(
                    'themes_stored' => \Idno\Core\site()->themes()->getStored(),
                    'theme'         => \Idno\Core\site()->themes()->get(),
                ))->draw('admin/themes');
                $t->title = 'Themes';
                $t->drawPage();
            }

            function postContent()
            {
                $this->adminGatekeeper(); // Admins only
                $theme  = $this->getInput('theme');
                $action = $this->getInput('action');
                if (defined('KNOWN_MULTITENANT_HOST')) {
                    $host = KNOWN_MULTITENANT_HOST;
                }
                if (
                    (
                        preg_match('/^[a-zA-Z0-9]+$/', $theme) &&
                        (
                            file_exists(\Idno\Core\site()->config()->path . '/Themes/' . $theme) ||
                            (
                                !empty($host) && (
                                    file_exists(\Idno\Core\site()->config()->path . '/hosts/' . $host . '/Themes/' . $theme)
                                )
                            )
                        )
                    )
                    || $theme == ''
                ) {
                    switch ($action) {
                        case 'install':
                            \Idno\Core\site()->config->config['theme'] = $theme;
                            \Idno\Core\site()->session()->addMessage('The theme was enabled.');
                            break;
                        case 'uninstall':
                            \Idno\Core\site()->config->config['theme'] = '';
                            break;
                    }
                    \Idno\Core\site()->config()->save();
                }
                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/themes/');
            }

        }

    }