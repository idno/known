<?php

    /**
     * Administration page: email settings
     */

    namespace Themes\Cherwell\Pages {

        use Idno\Entities\File;

        class Admin extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('admin/cherwell');
                $t->title = 'Theme Settings';
                $t->drawPage();

            }

            function postContent()
            {
                $this->adminGatekeeper(); // Admins only
                if (!empty($_FILES['background'])) {
                    if (in_array($_FILES['background']['type'], array('image/png', 'image/jpg', 'image/jpeg', 'image/gif'))) {
                        if (getimagesize($_FILES['background']['tmp_name'])) {
                            if ($background = \Idno\Entities\File::createFromFile($_FILES['background']['tmp_name'], $_FILES['background']['name'])) {
                                // Remove previous bg
                                if (!empty(\Idno\Core\site()->config()->cherwell['bg_id'])) {
                                    if ($file = File::getByID(\Idno\Core\site()->config()->cherwell['bg_id'])) {
                                        $file->delete();
                                    }
                                }
                                \Idno\Core\site()->config->config['cherwell']['bg_id'] = $background;
                                $background = \Idno\Core\site()->config()->getURL() . 'file/' . $background;
                                \Idno\Core\site()->config->config['cherwell']['bg'] = $background;
                                \Idno\Core\site()->config->save();
                            }
                        }
                    }
                }
                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/cherwell/');
            }

        }

    }