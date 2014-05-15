<?php

    namespace knownPlugins\Text {

        class ContentType extends \known\Common\ContentType {

            public $title = 'Post';
            public $category_title = 'Posts';
            public $entity_class = 'knownPlugins\\Text\\Entry';
            public $logo = '<i class="icon-align-left"></i>';

        }

    }