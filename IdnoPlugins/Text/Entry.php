<?php

    namespace IdnoPlugins\Text {

        class Entry extends \Idno\Common\Entity {

            function getTitle() {
                if (empty($this->title)) return 'Untitled';
                return $this->title;
            }

            function getDescription() {
                if (!empty($this->body)) return $this->body;
                return '';
            }

            /**
             * Entry objects have type 'article'
             * @return 'article'
             */
            function getActivityStreamsObjectType() {
                return 'article';
            }

        }

    }