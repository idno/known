<?php

    namespace knownPlugins\Comic {

        class ContentType extends \known\Common\ContentType {

            public $title = 'Comic';
            public $category_title = 'Comics';
            public $entity_class = 'knownPlugins\\Comic\\Comic';
            public $logo = '<i class="icon-qrcode"></i>';

        }

    }