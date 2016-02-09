<?php

    namespace IdnoPlugins\Text {

        use Idno\Core\Autosave;

        class Entry extends \Idno\Common\Entity
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

                // If we have a URL override, use it
                if (!empty($this->url)) {
                    return $this->url;
                }

                if (!empty($this->canonical)) {
                    return $this->canonical;
                }

                if (!$this->getSlug() && ($this->getID())) {
                    return \Idno\Core\Idno::site()->config()->url . 'entry/' . $this->getID() . '/' . $this->getPrettyURLTitle();
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

            /**
             * Retrieve icon
             * @return mixed|string
             */
            function getIcon()
            {
                $doc = @\DOMDocument::loadHTML($this->getDescription());
                if ($doc) {
                    $xpath = new \DOMXPath($doc);
                    $src   = $xpath->evaluate("string(//img/@src)");
                    if (!empty($src)) {
                        return $src;
                    }
                }
                return parent::getIcon();
            }

            function saveDataFromInput()
            {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                $body = \Idno\Core\Idno::site()->currentPage()->getInput('body');
                if (!empty($body)) {

                    $this->body  = $body;
                    $this->title = \Idno\Core\Idno::site()->currentPage()->getInput('title');
                    $this->tags  = \Idno\Core\Idno::site()->currentPage()->getInput('tags');
                    $access      = \Idno\Core\Idno::site()->currentPage()->getInput('access');
                    $this->setAccess($access);

                    if ($time = \Idno\Core\Idno::site()->currentPage()->getInput('created')) {
                        if ($time = strtotime($time)) {
                            $this->created = $time;
                        }
                    }

                    if ($this->publish($new)) {

                        $autosave = new Autosave();
                        $autosave->clearContext('entry');

                        if ($this->getAccess() == 'PUBLIC') {
                            \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
                        }

                        return true;
                    }
                } else {
                    \Idno\Core\Idno::site()->session()->addErrorMessage('You can\'t save an empty entry.');
                }

                return false;

            }

            function deleteData()
            {
                if ($this->getAccess() == 'PUBLIC') {
                    \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
                }
            }

        }

    }