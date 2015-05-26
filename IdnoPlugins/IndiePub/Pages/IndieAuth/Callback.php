<?php

    namespace IdnoPlugins\IndiePub\Pages\IndieAuth {

        use Idno\Core\Webservice;
        use Idno\Entities\User;

        class Callback extends \Idno\Common\Page
        {

            function getContent()
            {
                $user = \Idno\Entities\User::getOne(array('admin' => true)); // This is for single user sites; will retrieve the main user
                $code = $this->getInput('code');
                if (!empty($code)) {
                    $client   = new Webservice();
                    $response = Webservice::post('http://indieauth.com/auth', array(
                        'code'         => $code,
                        'redirect_uri' => \Idno\Core\site()->config()->getURL(),
                        'client_id'    => \Idno\Core\site()->config()->getURL()
                    ));
                    if ($response['response'] == 200) {
                        parse_str($response['content'], $content);
                        if (!empty($content['me']) && parse_url($content['me'], PHP_URL_HOST) == parse_url(\Idno\Core\site()->config()->getURL, PHP_URL_HOST)) {
                            $user                 = \Idno\Core\site()->session()->currentUser();
                            $user->indieauth_code = $code;
                            $user->save();
                            \Idno\Core\site()->session()->logUserOn($user);
                        } else {
                            \Idno\Core\site()->session()->addMessage("Couldn't log you in: the token hostname didn't match.");
                        }
                    } else {
                        \Idno\Core\site()->session()->addMessage("Uh oh! We got a " . $response['response'] . " response.");
                    }
                }

            }

        }

    }