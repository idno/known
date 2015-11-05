<?php

    /**
     * User mentions
     */

    namespace Idno\Pages\Search {

        use Idno\Pages\Homepage;

        class Tags extends \Idno\Common\Page
        {

            function getContent()
            {

                if (!empty($this->arguments[0])) {
                    $tag             = urldecode($this->arguments[0]);
                    $page            = new Homepage();
                    $page->arguments = ['all'];
                    $page->setInput('q', '#' . $tag);
                    $page->getContent();
                    exit;
                }

                $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL());

            }

        }

    }