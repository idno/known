<?php

    namespace knownPlugins\Photo {

        class Photo extends \known\Common\Entity {

            function getTitle() {
                if (empty($this->title)) {
                    return 'Untitled';
                } else {
                    return $this->title;
                }
            }

            function getDescription() {
                return $this->body;
            }

            /**
             * Photo objects have type 'image'
             * @return 'image'
             */
            function getActivityStreamsObjectType() {
                return 'image';
            }
	    
	    /**
	     * Extend json serialisable to include some extra data
	     */
	    public function jsonSerialize() {
		$object = parent::jsonSerialize();
		
		// Add some thumbs
		$object['thumbnails'] = [ ];
		$sizes = \known\Core\site()->events()->dispatch('photo/thumbnail/getsizes', new \known\Core\Event(array('sizes' => ['large' => 800, 'medium' => 400, 'small' => 200])));
		foreach ($sizes->data()['sizes'] as $label => $size) {
		    $varname = "thumbnail_{$label}";
		    $object['thumbnails'][$label] = $this->$varname;
		}
		
		return $object;
	    }

            /**
             * Saves changes to this object based on user input
             * @return bool
             */
            function saveDataFromInput() {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
                $this->title = \known\Core\site()->currentPage()->getInput('title');
                $this->body = \known\Core\site()->currentPage()->getInput('body');
                $this->setAccess('PUBLIC');

                // Get photo
                if ($new) {
                    if (!empty($_FILES['photo']['tmp_name'])) {
                        if (\known\Entities\File::isImage($_FILES['photo']['tmp_name'])) {
                            if ($photo = \known\Entities\File::createFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $_FILES['photo']['type'],true)) {
                                $this->attachFile($photo);
				
				// Now get some smaller thumbnails, with the option to override sizes
				$sizes = \known\Core\site()->events()->dispatch('photo/thumbnail/getsizes', new \known\Core\Event(array('sizes' => ['large' => 800, 'medium' => 400, 'small' => 200])));
				foreach ($sizes->data()['sizes'] as $label => $size) {
				    
				    $filename = $_FILES['photo']['name'];
				    
				    if ($thumbnail = \known\Entities\File::createThumbnailFromFile($_FILES['photo']['tmp_name'], "{$filename}_{$label}", $size)) {
					$varname = "thumbnail_{$label}";
					$this->$varname = \known\Core\site()->config()->url . 'file/' . $thumbnail;
					
					$varname = "thumbnail_{$label}_id";
					$this->$varname = substr($thumbnail,0,strpos($thumbnail,'/'));
				    }
				}
                                
                            } else {
                                \known\Core\site()->session()->addMessage('Image wasn\'t attached.');
                            }
                        } else {
                            \known\Core\site()->session()->addMessage('This doesn\'t seem to be an image ..');
                        }
                    } else {
                        \known\Core\site()->session()->addMessage('We couldn\'t access your image. Please try again.');
                        return false;
                    }
                }

                if ($this->save()) {
                    if ($new) {
                        $this->addToFeed();
                    } // Add it to the Activity Streams feed
                    \known\Core\Webmention::pingMentions($this->getURL(), \known\Core\site()->template()->parseURLs($this->getDescription()));
                    \known\Core\site()->session()->addMessage('Your photo was successfully saved.');
                    return true;
                } else {
                    return false;
                }

            }

        }

    }