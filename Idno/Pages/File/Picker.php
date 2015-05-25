<?php

    /**
     * File picker
     */

    namespace Idno\Pages\File {

        class Picker extends \Idno\Common\Page
        {

            function getContent()
            {
                $type = $this->getInput('type');
                if (in_array($type, ['image'])) {
                    $template = 'file/picker/' . $type;
                } else {
                    $template = 'file/picker';
                }

                $t          = \Idno\Core\site()->template();
                $t->title   = 'File picker';
                $t->hidenav = true;
                $t->body    = $t->draw($template);
                echo $t->draw('shell');
            }

            function post()
            {
                if (\Idno\Core\site()->session()->isLoggedOn()) {
                    if (!empty($_FILES['file']['tmp_name'])) {
                        if (!\Idno\Core\site()->triggerEvent("file/upload", [], true)) {
                            exit;
                        }
                        if ($file = \Idno\Entities\File::createFromFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type'], true)) {
                            $t       = \Idno\Core\site()->template();
                            $t->file = $file;
                            echo $t->draw('file/picker/donejs');
                            exit;
                        }
                    }
                }
            }

        }

    }