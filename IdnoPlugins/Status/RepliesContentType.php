<?php

    namespace IdnoPlugins\Status {

        class RepliesContentType extends \Idno\Common\ContentType {

            public $title = '@replies';
            public $category_title = 'Reply status updates';
            public $entity_class = 'IdnoPlugins\\Status\\Status';
            public $indieWebContentType = array('note','reply');

        }

    }