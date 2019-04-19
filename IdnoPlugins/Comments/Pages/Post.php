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

            $name_field = \Idno\Core\Bonita\Forms::obfuscateField('name');
            $url_field = \Idno\Core\Bonita\Forms::obfuscateField('url');

            $body      = strip_tags($this->getInput('body'));
            $name      = strip_tags($this->getInput($name_field));
            $url       = trim($this->getInput($url_field));
            $url2      = $this->getInput('url');
            $name2     = $this->getInput('name');
            $validator = $this->getInput('validator');

            if (!empty($url2) || !empty($name2)) {
                $this->deniedContent();
            }

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
                        $icon = \Idno\Core\Idno::site()->config()->getStaticURL(). 'gfx/users/default-'. str_pad($number, 2, '0', STR_PAD_LEFT) .'.png';
                    }
                    $object->addAnnotation('reply', $name, $url, $icon, $body);
                    $this->forward($object->getDisplayURL());
                }
            }

        }

    }

}

