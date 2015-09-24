<?php

    namespace IdnoPlugins\Status {

        class RepliesContentType extends \Idno\Common\ContentType {

            public $title = 'Replies';
            public $category_title = 'Replies';
            public $entity_class = 'IdnoPlugins\\Status\\Reply';
            public $indieWebContentType = array('reply');
            public $createable = false; // Don't show on new content bar
            public $hide = true;

        }

    }