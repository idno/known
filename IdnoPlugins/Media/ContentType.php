<?php

namespace IdnoPlugins\Media {

    class ContentType extends \Idno\Common\ContentType
    {

        public $title = 'Media';
        public $category_title = 'Media';
        public $entity_class = 'IdnoPlugins\\Media\\Media';
        public $indieWebContentType = array('media','audio','video');

    }

}
