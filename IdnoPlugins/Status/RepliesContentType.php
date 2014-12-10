<?php

    namespace IdnoPlugins\Status {

        class RepliesContentType extends \Idno\Common\ContentType {

            public $title = 'Replies';
            public $category_title = '@replies';
            public $entity_class = 'IdnoPlugins\\Status\\Status';
            public $indieWebContentType = array('reply');

        }

    }