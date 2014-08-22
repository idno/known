<?php

    namespace IdnoPlugins\Media {

        class ContentType extends \Idno\Common\ContentType {

            public $title = 'Audio';
            public $category_title = 'Streaming media';
            public $entity_class = 'IdnoPlugins\\Media\\Media';
            public $indieWebContentType = ['media'];

        }

    }