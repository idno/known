<?php

    namespace IdnoPlugins\Like {

        class Like extends \Idno\Common\Entity {

            function getTitle() {
                return strip_tags($this->body);
            }

            function getDescription() {
                $body = $this->body;
                if (!empty($this->description)) {
                    $body .= ' ' . $this->description;
                }
                return $body;
            }

            function getURL() {
                // If we have a URL override, use it
                if (!empty($this->url)) {
                    return $this->url;
                }

                if (!empty($this->canonical)) {
                    return $this->canonical;
                }
                if (!($this->getSlug()) && ($this->getID())) {
                    return \Idno\Core\site()->config()->url . 'bookmark/' . $this->getID() . '/' . $this->getPrettyURLTitle();
                } else {
                    return parent::getURL();
                }
            }

            /**
             * Like objects have type 'bookmark'
             * @return 'bookmark'
             */
            function getActivityStreamsObjectType() {
                return 'bookmark';
            }

            /**
             * Given a URL, returns the page title.
             * @param $Url
             * @return mixed
             */
            function getTitleFromURL($Url){
                $str = \Idno\Core\Webservice::file_get_contents($Url); //@file_get_contents($Url); 
                if(strlen($str)>0){
                    preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title);
                    return $title[1];
                } 
                return '';
            }

            /**
             * Saves changes to this object based on user input
             * @return true|false
             */
            function saveDataFromInput() {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                $body = \Idno\Core\site()->currentPage()->getInput('body');
                $description = \Idno\Core\site()->currentPage()->getInput('description');
                $tags = \Idno\Core\site()->currentPage()->getInput('tags');

                if ($time = \Idno\Core\site()->currentPage()->getInput('created')) {
                    if ($time = strtotime($time)) {
                        $this->created = $time;
                    }
                }

                $body = trim($body);
                if(filter_var($body, FILTER_VALIDATE_URL)){
                if (!empty($body)) {
                    $this->body = $body;
                    $this->description = $description;
                    $this->tags = $tags;
                    if ($title = $this->getTitleFromURL($body)) {
                        $this->pageTitle = $title;
                    } else {
                        $this->pageTitle = '';
                    }
                    $this->setAccess('PUBLIC');
                    if ($this->save()) {
                        if ($new) {
                            $this->addToFeed();
                        } // Add it to the Activity Streams feed
                        $result = \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->body));
                        $result = \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->description));
                        return true;
                    }
                } else {
                    \Idno\Core\site()->session()->addMessage('You can\'t like nothingness. I mean, maybe you can, but it\'s frowned upon.');
                }
                } else {
                    \Idno\Core\site()->session()->addMessage('That doesn\'t look like a valid URL.');
                }
                return false;

            }

            function deleteData() {
                \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getDescription()));
            }

        }

    }