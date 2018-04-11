<?php

namespace Idno\Core {

    class Vendor extends \Idno\Common\Component {

        /**
         * Retrieve notices (eg notifications that a new version has been released) from Known HQ
         * @return string
         */
        public static function getMessages() {

            if (!empty(\Idno\Core\Idno::site()->config()->noping)) {
                return '';
            }

            $results = Webservice::post('https://withknown.com/vendor-services/messages/', [
                        'url' => \Idno\Core\Idno::site()->config()->getURL(),
                        'title' => \Idno\Core\Idno::site()->config()->getTitle(),
                        'version' => Version::version(),
                        'public' => \Idno\Core\Idno::site()->config()->isPublicSite(),
                        'phpversion' => phpversion(),
                        'dbengine' => get_class(\Idno\Core\Idno::site()->db()),
                        'hub' => \Idno\Core\Idno::site()->config()->known_hub,
            ]);
             
            if ($results['response'] == 200) {
                return $results['content'];
            }
            
            return '';
        }

    }

}