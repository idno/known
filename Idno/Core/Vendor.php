<?php

namespace Idno\Core {

    class Vendor extends \Idno\Common\Component
    {

        /**
         * Retrieve notices (eg notifications that a new version has been released) from Known HQ
         * @return string
         */
        public static function getMessages()
        {

            if (!empty(\Idno\Core\Idno::site()->config()->noping)) {
                return '';
            }

            $results = Webservice::post('https://withknown.com/vendor-services/messages/', [
                        'version' => Version::version(),
            ]);

            if ($results['response'] == 200) {
                return $results['content'];
            }

            return '';
        }

    }

}
