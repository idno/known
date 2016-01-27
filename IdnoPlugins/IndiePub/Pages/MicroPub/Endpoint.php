<?php

    namespace IdnoPlugins\IndiePub\Pages\MicroPub {

        use Idno\Common\ContentType;
        use Idno\Entities\User;
        use IdnoPlugins\IndiePub\Pages\IndieAuth\Token;

        use DOMDocument;
        use DOMXPath;

        class Endpoint extends \Idno\Common\Page
        {

            private function getServiceAccountsFromHub()
            {
                $results = [];
                if (\Idno\Core\Idno::site()->hub()) {
                    $result = \Idno\Core\Idno::site()->hub()->makeCall('hub/user/syndication', [
                        'content_type' => 'note',
                    ]);
                    if (!empty($result['content'])) {
                        $content = $result['content'];

                        // parse value from the inputs with name="syndication[]".
                        // TODO consider serving JSON in addition to HTML from hub?
                        $doc = new DOMDocument();
                        $doc->loadHTML($content);
                        $toggles = (new DOMXPath($doc))->query('//*[@name="syndication[]"]');

                        foreach ($toggles as $toggle) {
                            $results[] = $toggle->getAttribute('value');
                        }
                    }
                }
                return $results;
            }

            function get($params = array())
            {
                $this->gatekeeper();
                if ($query = trim($this->getInput('q'))) {
                    switch ($query) {
                    case 'syndicate-to':
                        $account_strings = \Idno\Core\Idno::site()->syndication()->getServiceAccountStrings();
                        $account_data    = \Idno\Core\Idno::site()->syndication()->getServiceAccountData();
                        // TODO augment $account_data too
                        $account_strings = array_merge($account_strings, $this->getServiceAccountsFromHub());

                        if ($this->isAcceptedContentType('application/json')) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'syndicate-to'          => $account_strings,
                                'syndicate-to-expanded' => $account_data,
                            ], JSON_PRETTY_PRINT);
                        } else {
                            echo http_build_query([
                                "syndicate-to" => $account_strings,
                            ]);
                        }
                        break;
                    }
                }
            }

            function post()
            {
                $this->gatekeeper();
                // If we're here, we're authorized

                // Get details
                $type        = $this->getInput('h');
                $content     = $this->getInput('content');
                $name        = $this->getInput('name');
                $in_reply_to = $this->getInput('in-reply-to');
                $syndicate   = $this->getInput('mp-syndicate-to', $this->getInput('syndicate-to'));
                $like_of     = $this->getInput('like-of');
                $repost_of   = $this->getInput('repost-of');

                if ($type == 'entry') {
                    $type = 'article';
                    if (!empty($_FILES['photo'])) {
                        $type = 'photo';
                    }
                    else {
                        $photo_url = $this->getInput('photo');
                        if ($photo_url) {
                            $type      = 'photo';
                            $success   = $this->uploadFromUrl($photo_url);
                            if (!$success) {
                                \Idno\Core\Idno::site()->triggerEvent('indiepub/post/failure', ['page' => $this]);
                                $this->setResponse(500);
                                echo "Failed uploading photo from $photo_url";
                                exit;
                            }
                        }
                    }

                    if ($type == 'photo' && empty($name) && !empty($content)) {
                        $name    = $content;
                        $content = '';
                    }

                    if (empty($name)) {
                        $type = 'note';
                    }
                    if (!empty($like_of)) {
                        $type = 'like';
                    }
                    if (!empty($repost_of)) {
                        $type = 'repost';
                    }
                }

                // Get an appropriate plugin, given the content type
                if ($contentType = ContentType::getRegisteredForIndieWebPostType($type)) {

                    if ($entity = $contentType->createEntity()) {

                        error_log(var_export($entity, true));

                        if (is_array($content)) {
                            $content_value = '';
                            if (!empty($content['html'])) {
                                $content_value = $content['html'];
                            } else if (!empty($content['value'])) {
                                $content_value = $content['value'];
                            }
                        } else {
                            $content_value = $content;
                        }

                        $this->setInput('title', $name);
                        $this->setInput('body', $content_value);
                        $this->setInput('inreplyto', $in_reply_to);
                        $this->setInput('like-of', $like_of);
                        $this->setInput('repost-of', $repost_of);
                        $this->setInput('access', 'PUBLIC');
                        if ($created = $this->getInput('published')) {
                            $this->setInput('created', $created);
                        }
                        if (!empty($syndicate)) {
                            if (is_array($syndicate)) {
                                $syndication = $syndicate;
                            } else {
                                $syndication = array(trim(str_replace('.com', '', $syndicate)));
                            }
                            \Idno\Core\Idno::site()->logging()->log("Setting syndication: $syndication");
                            $this->setInput('syndication', $syndication);
                        }
                        if ($entity->saveDataFromInput()) {
                            $this->setResponse(201);
                            header('Location: ' . $entity->getURL());
                            exit;
                        } else {
                            $this->setResponse(500);
                            echo "Couldn't create {$type}";
                            exit;
                        }

                    }

                } else {

                    $this->setResponse(500);
                    echo "Couldn't find content type {$type}";
                    exit;

                }
            }

            /**
             * Micropub optionally allows uploading photos from a
             * URL. This method downloads the file at a URL to a
             * temporary location and puts it in the php $_FILES
             * array.
             */
            private function uploadFromUrl($photo_url)
            {
                $pathinfo = pathinfo(parse_url($photo_url, PHP_URL_PATH));
                switch ($pathinfo['extension']) {
                case 'jpg':
                case 'jpeg':
                    $mimetype = 'image/jpeg';
                    break;
                case 'png':
                    $mimetype = 'image/png';
                    break;
                case 'gif':
                    $mimetype = 'image/gif';
                    break;
                }

                $tmpname  = tempnam(sys_get_temp_dir(), 'indiepub_');
                $fp       = fopen($photo_url, 'rb');
                if ($fp) {
                    $success = file_put_contents($tmpname, $fp);
                    fclose($fp);
                }
                if ($success) {
                    $_FILES['photo'] = [
                        'tmp_name' => $tmpname,
                        'name'     => $pathinfo['basename'],
                        'size'     => filesize($tmpname),
                        'type'     => $mimetype,
                    ];
                }
                return $success;
            }

        }
    }
