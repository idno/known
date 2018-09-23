<?php

namespace IdnoPlugins\Photo {

    class ContentType extends \Idno\Common\ContentType
    {

        public $title = 'Photo';
        public $category_title = 'Photos';
        public $entity_class = 'IdnoPlugins\\Photo\\Photo';
        public $indieWebContentType = array('photo','picture');

    }

}

