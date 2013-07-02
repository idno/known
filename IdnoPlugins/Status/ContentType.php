<?php

    namespace IdnoPlugins\Status {

        class ContentType extends \Idno\Common\ContentType {

            public $title = 'Status update';
            public $entity_class = 'IdnoPlugins\\Status\\Status';
            public $indieWebContentType = ['note','reply'];

        }

    }