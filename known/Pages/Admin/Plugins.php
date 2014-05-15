<?php

    /**
     * Plugin administration
     */

    namespace known\Pages\Admin {

        /**
         * Default class to serve the homepage
         */
        class Plugins extends \known\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t        = \known\Core\site()->template();
                $t->body  = $t->__(array(
                    'plugins_stored' => \known\Core\site()->plugins()->getStored(),
                    'plugins_loaded' => \known\Core\site()->plugins()->getLoaded(),
                ))->draw('admin/plugins');
                $t->title = 'Plugins';
                $t->drawPage();
            }

            function postContent()
            {
                $this->adminGatekeeper(); // Admins only
                $plugin = $this->getInput('plugin');
                $action = $this->getInput('action');
                if (preg_match('/^[a-zA-Z0-9]+$/', $plugin) && file_exists(\known\Core\site()->config()->path . '/knownPlugins/' . $plugin)) {
                    switch ($action) {
                        case 'install':
                            \known\Core\site()->config->config['plugins'][] = $plugin;
                            \known\Core\site()->session()->addMessage('The plugin was installed.');
                            break;
                        case 'uninstall':
                            if (($key = array_search($plugin, \known\Core\site()->config->config['plugins'])) !== false) {
                                unset(\known\Core\site()->config->config['plugins'][$key]);
                                \known\Core\site()->session()->addMessage('The plugin was uninstalled.');
                            }
                            break;
                    }
                    \known\Core\site()->config()->save();
                }
                $this->forward('/admin/plugins/');
            }

        }

    }