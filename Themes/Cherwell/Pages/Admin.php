<?php

    /**
     * Administration page: email settings
     */

    namespace Themes\Cherwell\Pages {

        use Idno\Entities\File;
        use Idno\Entities\User;

        class Admin extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $users = User::get(array('admin' => true));
                $t        = \Idno\Core\Idno::site()->template();
                $t->body  = $t->__(array('users' => $users))->draw('admin/cherwell');
                $t->title = 'Theme Settings';
                $t->drawPage();

            }

            function postContent()
            {
                $this->adminGatekeeper(); // Admins only
                if ($profile_user = $this->getInput('profile_user')) {
                    \Idno\Core\Idno::site()->config->config['cherwell']['profile_user'] = $profile_user;
                }
                if (!empty($_FILES['background']) && $this->getInput('action') != 'clear') {
                    if (in_array($_FILES['background']['type'], array('image/png', 'image/jpg', 'image/jpeg', 'image/gif'))) {
                        if (getimagesize($_FILES['background']['tmp_name'])) {
                            if ($background = \Idno\Entities\File::createFromFile($_FILES['background']['tmp_name'], $_FILES['background']['name'])) {
                                // Remove previous bg
                                if (!empty(\Idno\Core\Idno::site()->config()->cherwell['bg_id'])) {
                                    if ($file = File::getByID(\Idno\Core\Idno::site()->config()->cherwell['bg_id'])) {
                                        if (is_callable([$file,'delete'])) {        // TODO: really need some abstraction here.
                                            $file->delete();
                                        } else if (is_callable([$file,'remove'])) {
                                            $file->remove();
                                        }
                                    }
                                }
                                \Idno\Core\Idno::site()->config->config['cherwell']['bg_id'] = $background;
                                $background = \Idno\Core\Idno::site()->config()->getDisplayURL() . 'file/' . $background;
                                \Idno\Core\Idno::site()->config->config['cherwell']['bg'] = $background;
                            }
                        }
                    }
                } else {
                    // Remove previous bg
                    if (!empty(\Idno\Core\Idno::site()->config()->cherwell['bg_id'])) {
                        if ($file = File::getByID(\Idno\Core\Idno::site()->config()->cherwell['bg_id'])) {
                            if (is_callable([$file,'delete'])) {
                                $file->delete();
                            } else if (is_callable([$file,'remove'])) {
                                $file->remove();
                            }
                        }
                    }
                    \Idno\Core\Idno::site()->config->cherwell = [];
                }
                \Idno\Core\Idno::site()->config->save();
                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/cherwell/');
            }

        }

    }