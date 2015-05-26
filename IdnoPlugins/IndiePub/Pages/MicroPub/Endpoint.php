<?php

    namespace IdnoPlugins\IndiePub\Pages\MicroPub {

        use Idno\Common\ContentType;
        use Idno\Entities\User;

        class Endpoint extends \Idno\Common\Page
        {

            function get()
            {

                $this->setResponse(403);
                echo '?';

            }

            function post()
            {

                $headers = $this->getallheaders();
                $user    = \Idno\Entities\User::getOne(array('admin' => true));
                \Idno\Core\site()->session()->refreshSessionUser($user);
                $indieauth_tokens = $user->indieauth_tokens;

                if (!empty($headers['Authorization'])) {
                    $token = $headers['Authorization'];
                    $token = trim(str_replace('Bearer', '', $token));
                } else if ($token = $this->getInput('access_token')) {
                    $token = trim($token);
                }
                
                $user_token = $user->getAPIkey();

                if (!empty($indieauth_tokens[$token]) || $token == $user_token) {

                    // If we're here, we're authorized

                    // Get details
                    $type        = $this->getInput('h');
                    $content     = $this->getInput('content');
                    $name        = $this->getInput('name');
                    $in_reply_to = $this->getInput('in-reply-to');
                    $syndicate   = $this->getInput('syndicate-to');

                    if ($type == 'entry') {
                        if (!empty($_FILES['photo'])) {
                            $type = 'photo';
                            if (empty($name) && !empty($content)) {
                                $name = $content; $content = '';
                            }
                        } else if (empty($name)) {
                            $type = 'note';
                        } else {
                            $type = 'article';
                        }
                    }

                    // Get an appropriate plugin, given the content type
                    if ($contentType = ContentType::getRegisteredForIndieWebPostType($type)) {

                        if ($entity = $contentType->createEntity()) {

                            $this->setInput('title', $name);
                            $this->setInput('body', $content);
                            $this->setInput('inreplyto', $in_reply_to);
                            if ($created = $this->getInput('published')) {
                                $this->setInput('created', $created);
                            }
                            if (!empty($syndicate)) {
                                $syndication = array(trim(str_replace('.com', '', $syndicate)));
                                $this->setInput('syndication', $syndication);
                            }
                            if ($entity->saveDataFromInput()) {
                                //$this->setResponse(201);
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

                $this->setResponse(403);
                echo 'Bad token';

            }

        }

    }
