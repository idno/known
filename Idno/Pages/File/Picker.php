<?php

    /**
     * File picker
     */

namespace Idno\Pages\File {

    use Idno\Core\Idno;

    class Picker extends \Idno\Common\Page
    {

        function getContent()
        {

            $this->setAsset("exif-js", \Idno\Core\Idno::site()->config()->getStaticURL() . 'vendor/npm-asset/exif-js/exif.js', 'javascript');

            $template   = 'file/picker/image';
            $t          = \Idno\Core\Idno::site()->template();
            $t->title   = 'Image picker';
            $t->hidenav = true;
            $t->body    = $t->draw($template);
            $t->drawPage();
        }

        function post()
        {
            if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                $tmp_file = \Idno\Core\Input::getFile('file');
                 
                if (!empty($tmp_file['tmp_name'])) {
                    if (!\Idno\Core\Idno::site()->events()->triggerEvent("file/upload", [], true)) {
                        exit;
                    }
                    if (\Idno\Entities\File::isImage($tmp_file['tmp_name'])) {
                        $return = false;
                        $file   = false;
                        if ($file = \Idno\Entities\File::createThumbnailFromFile($tmp_file['tmp_name'], $tmp_file['name'], 1024)) {

                            \Idno\Core\Idno::site()->logging()->debug("Creating new file from thumbnail as {$file}");

                            $return           = true;
                            $returnfile       = new \stdClass;
                            $returnfile->file = ['_id' => $file];
                            $file             = $returnfile;
                        } else if ($file = \Idno\Entities\File::createFromFile($tmp_file['tmp_name'], $tmp_file['name'], $tmp_file['type'], true, true)) {
                            \Idno\Core\Idno::site()->logging()->debug("Creating new file");

                            $return = true;
                        }
                        if ($return) {
                            $t       = \Idno\Core\Idno::site()->template();
                            $t->file = $file;
                            $content = $t->draw('file/picker/donejs');
                            \Idno\Core\Idno::site()->response()->setContent($content);
                            \Idno\Core\Idno::site()->sendResponse();
                        }
                    } else {
                        Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_("You can only upload images."));
                    }
                }
                $this->forward(\Idno\Core\Idno::site()->request()->server->get('HTTP_REFERER'));
            }
        }

    }

}

