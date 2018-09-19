<?php

    namespace IdnoPlugins\Status {

        class ContentType extends \Idno\Common\ContentType {

            public $title = 'Status update';
            public $category_title = 'Status updates';
            public $entity_class = 'IdnoPlugins\\Status\\Status';
            public $indieWebContentType = array('note');

        }

    }
    