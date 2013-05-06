<?php

    namespace IdnoPlugins\Text {

        class PageEdit extends \Idno\Common\Page {

            function getContent() {
                $t = \Idno\Core\site()->template();
                $t->__(array(
                ))->draw('plugins/text/edit');
            }

        }

    }