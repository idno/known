<?php

namespace IdnoPlugins\Status {

    class Status extends \Idno\Common\Entity
    {

        /**
         * Create an appropriate status class.
         * Returns an appropriate new class, either a Status or a Reply, depending on whether it is in reply to something.
         * @return Status|Reply
         */
        public static function factory()
        {
            $inreplyto = \Idno\Core\Idno::site()->currentPage()->getInput('inreplyto');
            $body      = \Idno\Core\Idno::site()->currentPage()->getInput('body');

            if (!empty($inreplyto)) {
                return new Reply();
            }

            if ($body[0] == '@') {
                return new Reply();
            }

            return new Status();
        }

        function getTitle()
        {
            $title = trim($this->getShortDescription(7));
            if (empty($title)) {
                $title = \Idno\Core\Idno::site()->language('Status update');
            } else if ($title != trim($this->getShortDescription())) $title .= ' ...';

            return $title;
        }

        function getDescription()
        {
            $body = $this->body;
            if (!empty($this->inreplyto)) {
                if (is_array($this->inreplyto)) {
                    foreach ($this->inreplyto as $inreplyto) {
                        $body = '<a href="' . $inreplyto . '" class="u-in-reply-to"></a>' . $body;
                    }
                } else {
                    $body = '<a href="' . $this->inreplyto . '" class="u-in-reply-to"></a>' . $body;
                }
            }
            if (!empty($this->syndicatedto)) {
                foreach ($this->syndicatedto as $syndicated) {
                    $body = '<a href="' . $syndicated . '" class="u-in-reply-to"></a>' . $body;
                }
            }

            return $body;
        }

        /**
         * Status objects have type 'note'
         * @return 'note'
         */
        function getActivityStreamsObjectType()
        {
            return 'note';
        }

        function getMetadataForFeed()
        {
            $meta = array('type' => 'status');
            if ($this->inreplyto) {
                $meta['in-reply-to'] = $this->inreplyto;
                $meta['type'] = 'reply';
            }
            return $meta;
        }

        /**
         * Saves changes to this object based on user input
         * @return true|false
         */
        function saveDataFromInput()
        {

            if (empty($this->_id)) {
                $new = true;
            } else {
                $new = false;
            }
            $body      = \Idno\Core\Idno::site()->currentPage()->getInput('body');
            $inreplyto = \Idno\Core\Idno::site()->currentPage()->getInput('inreplyto');
            $tags      = \Idno\Core\Idno::site()->currentPage()->getInput('tags');
            $access    = \Idno\Core\Idno::site()->currentPage()->getInput('access');

            if ($time = \Idno\Core\Idno::site()->currentPage()->getInput('created')) {
                if ($time = strtotime($time)) {
                    $this->created = $time;
                }
            }

            if (!empty($body) || ('0' === $body)) {
                $this->body      = $body;
                $this->tags      = $tags;

                // TODO fetch syndicated reply targets asynchronously (or maybe on-demand, when syndicating?)
                if (!empty($inreplyto)) {

                    $this->inreplyto = $inreplyto;

                    if (is_array($inreplyto)) {
                        foreach ($inreplyto as $inreplytourl) {
                            if (!empty($inreplytourl)) {
                                $this->syndicatedto = \Idno\Core\Webmention::addSyndicatedReplyTargets($inreplytourl, $this->syndicatedto);
                            }
                        }
                    } else {
                        $this->syndicatedto = \Idno\Core\Webmention::addSyndicatedReplyTargets($inreplyto);
                    }
                }
                $this->setAccess($access);
                if ($this->publish($new)) {

                    if ($this->getAccess() == 'PUBLIC') {
                        \Idno\Core\Idno::site()->queue()->enqueue('default', 'webmention/sendall', [
                            'source' => $this->getURL(),
                            'text' => \Idno\Core\Idno::site()->template()->parseURLs($this->getDescription()),
                        ]);
                    }

                    // Queue link preview fetching for the first URL in the body
                    $firstUrl = self::extractFirstUrl($body);
                    if (!empty($firstUrl)) {
                        \Idno\Core\Idno::site()->queue()->enqueue('default', 'linkpreview/fetch', [
                            'entity_id' => (string) $this->_id,
                            'url' => $firstUrl,
                        ]);
                    }

                    return true;
                }
            } else {
                \Idno\Core\Idno::site()->session()->addErrorMessage(\Idno\Core\Idno::site()->language('You can\'t save an empty status update.'));
            }

            return false;

        }

        /**
         * Extract the first HTTP(S) URL from text
         * @param string $text
         * @return string|null
         */
        public static function extractFirstUrl($text)
        {
            if (preg_match('/(?<!=)(?<!["\'])((ht|f)tps?:\/\/[^\s<>"\']+)/i', $text, $matches)) {
                // Strip trailing punctuation
                $url = rtrim($matches[1], '.,!?;:)');
                return $url;
            }
            return null;
        }

        function deleteData()
        {
            if ($this->getAccess() == 'PUBLIC') {
                \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getDescription()));
            }
        }

    }

}
