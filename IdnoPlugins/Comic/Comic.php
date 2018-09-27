<?php

namespace IdnoPlugins\Comic {

    class Comic extends \Idno\Common\Entity
    {

        function getTitle()
        {
            if (empty($this->title)) return \Idno\Core\Idno::site()->language()->_('Untitled');

            return $this->title;
        }

        function getDescription()
        {
            if (!empty($this->body)) return $this->body;

            return '';
        }

        function getURL()
        {
            // If we have a URL override, use it
            if (!empty($this->url)) {
                return $this->url;
            }

            if (!empty($this->canonical)) {
                return $this->canonical;
            }
            if (($this->getID())) {
                return \Idno\Core\Idno::site()->config()->url . 'comic/' . $this->getID() . '/' . $this->getPrettyURLTitle();
            } else {
                return parent::getURL();
            }
        }

        /**
         * Entry objects have type 'article'
         * @return 'article'
         */
        function getActivityStreamsObjectType()
        {
            return 'article';
        }

        function saveDataFromInput()
        {

            if (empty($this->_id)) {
                $new = true;
            } else {
                $new = false;
            }

            if ($new) {
                if (!\Idno\Core\Idno::site()->triggerEvent("file/upload", [], true)) {
                    return false;
                }
            }

            $body = \Idno\Core\Idno::site()->currentPage()->getInput('body');
            if (!empty($_FILES['comic']['tmp_name']) || !empty($this->_id)) {
                $this->body        = $body;
                $this->title       = \Idno\Core\Idno::site()->currentPage()->getInput('title');
                $this->description = \Idno\Core\Idno::site()->currentPage()->getInput('description');

                if ($time = \Idno\Core\Idno::site()->currentPage()->getInput('created')) {
                    if ($time = strtotime($time)) {
                        $this->created = $time;
                    }
                }

                if (!empty($_FILES['comic']['tmp_name'])) {
                    if (\Idno\Entities\File::isImage($_FILES['comic']['tmp_name'])) {
                        if ($size = getimagesize($_FILES['comic']['tmp_name'])) {
                            $this->width  = $size[0];
                            $this->height = $size[1];
                        }
                        if ($comic = \Idno\Entities\File::createFromFile($_FILES['comic']['tmp_name'], $_FILES['comic']['name'], $_FILES['comic']['type'], true)) {
                            $this->attachFile($comic);
                        }
                    }
                }
                $this->setAccess(\Idno\Core\Idno::site()->currentPage()->getInput('access', 'PUBLIC'));
                if ($this->publish($new)) {

                    if ($this->getAccess() == 'PUBLIC') {
                        \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getDescription()));
                    }

                    return true;
                }
            } else {
                \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language()->_('You can\'t save an empty comic.'));
            }

            return false;

        }

        function deleteData()
        {
            if ($this->getAccess() == 'PUBLIC') {
                \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getDescription()));
            }
        }

    }

}

