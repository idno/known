<?php

namespace IdnoPlugins\Text {

    class ContentType extends \Idno\Common\ContentType
    {

        public $title = 'Post';
        public $category_title = 'Posts';
        public $entity_class = 'IdnoPlugins\\Text\\Entry';
        public $logo = '<i class="icon-align-left"></i>';
        public $indieWebContentType = array('article','entry');

    }

}

