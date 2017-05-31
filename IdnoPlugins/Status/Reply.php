<?php

    namespace IdnoPlugins\Status {

        class Reply extends \IdnoPlugins\Status\Status {

            function getMetadataForFeed() {
                return array(
                    'type' => 'reply',
                    'in-reply-to' => $this->inreplyto
                );
            }

        }

    }
