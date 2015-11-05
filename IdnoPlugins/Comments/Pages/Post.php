<?php

    namespace IdnoPlugins\Comments\Pages {

        use Idno\Common\Entity;
        use Idno\Common\Page;
        use Idno\Core\Webmention;
        use Idno\Core\Webservice;

        class Post extends Page
        {

            function postContent()
            {

                $body      = strip_tags($this->getInput('body'));
                $name      = strip_tags($this->getInput('name'));
                $url       = trim($this->getInput('url'));
                $url2       = trim($this->getInput('url-2'));
                $validator = $this->getInput('validator');

                if (!empty($url2)) {
                    $this->deniedContent();
                }

                $this->referrerGatekeeper();
                
                if (!empty($body) && !empty($name) && !empty($validator)) {
                    if ($object = Entity::getByUUID($validator)) {
                        if ($url = Webservice::sanitizeURL($url)) {
                            if ($content = Webservice::get($url)) {
                                if ($content['response'] == '200') {
                                    $icon = Webmention::getIconFromWebsiteContent($content['content'], $url);
                                }
                            }
                        }
                        if (empty($icon)) {
                            $bn = hexdec(substr(md5($url), 0, 15));
                            $number = 1 + ($bn % 5);
                            $icon = \Idno\Core\Idno::site()->config()->url . 'gfx/users/default-'. str_pad($number, 2, '0', STR_PAD_LEFT) .'.png';
                        }
                        $object->addAnnotation('reply', $name, $url, $icon, $body);
                        $this->forward($object->getDisplayURL());
                    }
                }

            }

        }

    }