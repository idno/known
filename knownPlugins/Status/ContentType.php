<?php

    namespace knownPlugins\Status {

        class ContentType extends \known\Common\ContentType {

            public $title = 'Status update';
            public $category_title = 'Status updates';
            public $entity_class = 'knownPlugins\\Status\\Status';
            public $indieWebContentType = ['note','reply'];

        }

    }