<?php

    /**
     * User mentions
     */

    namespace Idno\Pages\Search {

        class Tags extends \Idno\Common\Page
        {

            function getContent()
            {

                if (!empty($this->arguments[0])) {
                    $tag = $this->arguments[0];
                    $this->forward(\Idno\Core\site()->config()->getDisplayURL() . 'content/all/?q=' . urlencode('#' . $tag));
                }

                $this->forward(\Idno\Core\site()->config()->getDisplayURL());

            }

        }

    }