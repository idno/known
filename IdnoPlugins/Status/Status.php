<?php

    namespace IdnoPlugins\Status {

        class Status extends \Idno\Common\Entity {

            function getTitle() {
                return $this->body;
            }

            function getDescription() {
                return $this->body;
            }

            /**
             * Status objects have type 'note'
             * @return 'note'
             */
            function getActivityStreamsObjectType() {
                return 'note';
            }

        }

    }