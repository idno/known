<?php


namespace IdnoPlugins\OAuth2Client\Entities;

use Idno\Core\Idno;

class OAuth2Client extends \Idno\Entities\BaseObject
{

    public function getURL()
    {
        return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'oauth2/authorise/' . $this->getID() . '/';
    }

    public function getEditURL(): string
    {
        return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/oauth2client/' . $this->getID();
    }

    /**
     * Attempt a number of different ways to retrieve a public key from a url
     * @param string $url
     * @return string|null
     */
    public function getPublicKeyFromURL(string $url): ?string
    {

        $publickey = \Idno\Core\Webservice::file_get_contents($url);

        if (!empty($publickey)) {

            // Json key?
            $json = json_decode($publickey, true);
            if (!empty($json)) {

                // Try a bunch of variations
                foreach ([
                             'public_key',
                             'publickey',
                             'pk',
                             'pub_key',
                             'pubkey'
                         ] as $arraykey) {

                    if (!empty($json[$arraykey])) {
                        $publickey = $json[$arraykey];
                    }
                }
            }

            // In correct format?
            if (strpos($publickey, 'BEGIN PUBLIC KEY') === false) {
                $pubkey = "-----BEGIN PUBLIC KEY-----\n";
                $pubkey .= $publickey;
                $pubkey .= "\n-----END PUBLIC KEY-----";

                $publickey = $pubkey;
            }

            return $publickey;

        }


        return null;
    }

    public function saveDataFromInput()
    {

        if (empty($this->_id)) {
            $new = true;
        } else {
            $new = false;
        }

        // Save variables
        foreach ([
                     'label', 'client_id', 'client_secret', 'redirect_uri', 'url_authorise', 'url_access_token', 'url_resource', 'scopes', 'publickey_url', 'federation'
                 ] as $input) {

            $this->$input = \Idno\Core\Idno::site()->currentPage()->getInput($input);

        }

        if (!empty($this->publickey_url)) {

            $publickey = $this->getPublicKeyFromURL($this->publickey_url);

            if (empty($publickey)) {
                \Idno\Core\site()->session()->addErrorMessage(Idno::site()->language()->_('Public key could not be retrieved from %s', [$this->publickey_url]));
            }

            $this->publickey = $publickey;
        }

        // Save button
        if ($file = \Idno\Core\Input::getFiles('signin_button')) {

            if (!empty($file['tmp_name'])) {

                if (\Idno\Entities\File::isImage($file['tmp_name']) || \Idno\Entities\File::isSVG($file['tmp_name'], $file['name'])) {

                    if ($button = \Idno\Entities\File::createFromFile($file['tmp_name'], $file['name'], $file['type'], true, true)) {
                        $this->attachFile($button);
                    }
                }

            }


        }

        return $this->save(true);
    }

    function save($overrideAccess = true)
    {
        return parent::save($overrideAccess);
    }


}