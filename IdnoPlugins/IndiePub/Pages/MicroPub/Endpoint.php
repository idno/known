<?php

    namespace IdnoPlugins\IndiePub\Pages\MicroPub {

        use Idno\Common\ContentType;
        use Idno\Entities\User;
        use IdnoPlugins\IndiePub\Pages\IndieAuth\Token;

        use DOMDocument;
        use DOMXPath;

        class Endpoint extends \Idno\Common\Page
        {

            /**
             * Fetch syndication endpoints from Convoy.
             *
             * @param array $account_strings flat list of syndication
             *   IDs
             * @param array $account_data list of complex account data
             *   conforming to
             *   http://micropub.net/draft/#syndication-targets
             */
            private function getServiceAccountsFromHub(&$account_strings, &$account_data)
            {
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
                            $uid  = $toggle->getAttribute('value');

                            $account = strip_tags($toggle->getAttribute('data-on'));
                            $service = ucwords(explode('::', $uid, 2)[0]);

                            $name =  "$account on $service";
                            $name = trim(preg_replace('/\s+/u', ' ', $name));

                            $account_strings[] = $uid;
                            $account_data[]    = ['uid' => $uid, 'name' => $name];
                        }
                    }
                }
            }

            function get($params = array())
            {
                $this->gatekeeper();
                if ($query = trim($this->getInput('q'))) {
                    switch ($query) {
                    case 'syndicate-to':
                        $account_strings = \Idno\Core\Idno::site()->syndication()->getServiceAccountStrings();
                        $account_data    = \Idno\Core\Idno::site()->syndication()->getServiceAccountData();
                        $this->getServiceAccountsFromHub($account_strings, $account_data);

                        if ($this->isAcceptedContentType('application/json')) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'syndicate-to' => $account_data,
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

                \Idno\Core\Idno::site()->triggerEvent('indiepub/post/start', ['page' => $this]);

                // Get details
                $type        = $this->getInput('h', 'entry');
                $content     = $this->getInput('content');
                $name        = $this->getInput('name');
                $in_reply_to = $this->getInput('in-reply-to');
                $syndicate   = $this->getInput('mp-syndicate-to', $this->getInput('syndicate-to'));
                $posse_link  = $this->getInput('syndication');
                $like_of     = $this->getInput('like-of');
                $repost_of   = $this->getInput('repost-of');
                $categories  = $this->getInput('category');
                $rsvp        = $this->getInput('rsvp');
                $mp_type     = $this->getInput('mp-type');
                if (!empty($mp_type)) {
                   $type = $mp_type;
                }

                if ($type == 'entry') {
                    $type = 'note';

                    if (!empty($_FILES['photo'])) {
                        $type = 'photo';
                    } else if ($photo_url = $this->getInput('photo')) {
                        $type      = 'photo';
                        $success   = $this->uploadFromUrl($photo_url);
                        if (!$success) {
                            \Idno\Core\Idno::site()->triggerEvent('indiepub/post/failure', ['page' => $this]);
                            $this->setResponse(500);
                            echo "Failed uploading photo from $photo_url";
                            exit;
                        }
                    } else if (!empty($name)) {
                        $type = 'article';
                    }
                }
                if ($type == 'checkin')  {
                    $place_name = $this->getInput('place_name');
                    $location = $this->getInput('location');
                    $photo = $this->getInput('photo');
                    $latlong = explode(",",$location);
                    $lat = str_ireplace("geo:", "", $latlong[0]);
                    $long = $latlong[1];
                    $q = \IdnoPlugins\Checkin\Checkin::queryLatLong($lat, $long);
                    $user_address = $q['display_name'];
                    if (!empty($_FILES['photo'])) {
                        $id = \Idno\Entities\File::createFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $_FILES['photo']['type']) ;
                        $photo = \Idno\Core\Idno::site()->config()->url . 'file/' . $id;
                    }
                    if (!empty($photo)) {
                        $htmlPhoto = '<p><img style="display: block; margin-left: auto; margin-right: auto;" src="' . $photo . '" alt="' . $place_name . '"  /></p>';
                    }
                }
                if ($type == 'photo' && empty($name) && !empty($content)) {
                    $name    = $content;
                    $content = '';
                }
                if (!empty($like_of)) {
                    $type = 'like';
                }
                if (!empty($repost_of)) {
                    $type = 'repost';
                }
                if (!empty($rsvp)) {
                    $type = 'rsvp';
                }

                // setting all categories as hashtags into content field
                if (is_array($categories)) {
                    $hashtags = "";
                    foreach ($categories as $category) {
                        $category = trim($category);
                        if ($category) {
                            if (str_word_count($category) > 1) {
                                $category = str_replace("'"," ",$category);
                                $category = ucwords($category);
                                $category = str_replace(" ","",$category);
                            }
                            $hashtags .= " #$category";
                        }
                    }
                    $title_words = explode(" ", $name);
                    $name = "";
                    foreach ($title_words as $word) {
                        if (substr($word,0,1) !== "#") {
                            $name .= "$word ";
                        }
                    }
                }

                // Get an appropriate plugin, given the content type
                if ($contentType = ContentType::getRegisteredForIndieWebPostType($type)) {
                    if ($entity = $contentType->createEntity()) {
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
                        if (!empty($posse_link)) {
                            $posse_service = preg_replace('/^(www\.|m\.)?(.+?)(\.com|\.org|\.net)?$/', '$2', parse_url($posse_link, PHP_URL_HOST));
                            $entity->setPosseLink($posse_service, $posse_link, '', '');
                        }
                        $hashtags = (empty($hashtags) ? "" : "<p>".$hashtags."</p>");
                        $htmlPhoto    = (empty($htmlPhoto) ? "" : "<p>".$htmlPhoto."</p>");
                        $this->setInput('title', $name);
                        $this->setInput('body', $htmlPhoto.$content_value.$hashtags);
                        $this->setInput('inreplyto', $in_reply_to);
                        $this->setInput('like-of', $like_of);
                        $this->setInput('repost-of', $repost_of);
                        $this->setInput('rsvp', $rsvp);
                        $this->setInput('access', 'PUBLIC');
                        if ($type ==  'checkin') {
                            $this->setInput('lat', $lat);
                            $this->setInput('long', $long);
                            $this->setInput('user_address', $user_address);
                            $this->setInput('placename',$place_name);
                        }
                        if ($created = $this->getInput('published')) {
                            $this->setInput('created', $created);
                        }
                        if (!empty($syndicate)) {
                            if (is_array($syndicate)) {
                                $syndication = $syndicate;
                            } else {
                                $syndication = array(trim(str_replace('.com', '', $syndicate)));
                            }
                            \Idno\Core\Idno::site()->logging()->info("Setting syndication: $syndication");
                            $this->setInput('syndication', $syndication);
                        }
                        if ($entity->saveDataFromInput()) {
                            \Idno\Core\Idno::site()->triggerEvent('indiepub/post/success', ['page' => $this, 'object' => $entity]);
                            $this->setResponse(201);
                            header('Location: ' . $entity->getURL());
                            exit;
                        } else {
                            \Idno\Core\Idno::site()->triggerEvent('indiepub/post/failure', ['page' => $this]);
                            $this->setResponse(500);
                            echo "Couldn't create {$type}";
                            exit;
                        }

                    }

                } else {
                    \Idno\Core\Idno::site()->triggerEvent('indiepub/post/failure', ['page' => $this]);
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
