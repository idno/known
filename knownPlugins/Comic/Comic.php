<?php

    namespace knownPlugins\Comic {

        class Comic extends \known\Common\Entity
        {

            function getTitle()
            {
                if (empty($this->title)) return 'Untitled';

                return $this->title;
            }

            function getDescription()
            {
                if (!empty($this->body)) return $this->body;

                return '';
            }

            function getURL()
            {
                if (($this->getID())) {
                    return \known\Core\site()->config()->url . 'comic/' . $this->getID() . '/' . $this->getPrettyURLTitle();
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
                $body = \known\Core\site()->currentPage()->getInput('body');
                if (!empty($_FILES['comic']['tmp_name']) || !empty($this->_id)) {
                    $this->body        = $body;
                    $this->title       = \known\Core\site()->currentPage()->getInput('title');
                    $this->description = \known\Core\site()->currentPage()->getInput('description');
                    if (!empty($_FILES['comic']['tmp_name'])) {
                        if (\known\Entities\File::isImage($_FILES['comic']['tmp_name'])) {
                            if ($size = getimagesize($_FILES['comic']['tmp_name'])) {
                                $this->width  = $size[0];
                                $this->height = $size[1];
                            }
                            if ($comic = \known\Entities\File::createFromFile($_FILES['comic']['tmp_name'], $_FILES['comic']['name'], $_FILES['comic']['type'], true)) {
                                $this->attachFile($comic);
                            }
                        }
                    }
                    $this->setAccess('PUBLIC');
                    if ($this->save()) {
                        if ($new) {
                            // Add it to the Activity Streams feed
                            $this->addToFeed();
                        }
                        \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
                        \known\Core\site()->session()->addMessage('Your comic was successfully saved.');

                        return true;
                    }
                } else {
                    \known\Core\site()->session()->addMessage('You can\'t save an empty comic.');
                }

                return false;

            }

            function deleteData()
            {
                \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
            }

        }

    }