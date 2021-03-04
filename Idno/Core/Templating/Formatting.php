<?php


namespace Idno\Core\Templating {

    use \Idno\Core\Idno;

    trait Formatting
    {

        /**
         * Automatically adds paragraph tags (etc) to a given piece of unformatted or semi-formatted text.
         *
         * @param  $html
         * @return \false|string
         */
        function autop($html)
        {
            $html = Idno::site()->events()->triggerEvent('text/format', [], $html);

            $autop = new \mapkyca\autop\MrClayAutoP();

            return $autop->process($html);
        }

        /**
         * Wrapper for those on UK spelling.
         *
         * @param  $html
         * @return mixed
         */
        function sanitise_html($html)
        {
            return $this->sanitize_html($html);
        }

        /**
         * Sanitize HTML in a large block of text, removing XSS and other vulnerabilities.
         * This works by calling the text/filter event, as well as any built-in purifier.
         *
         * @param type $html
         */
        function sanitize_html($html)
        {
            $html = Idno::site()->events()->triggerEvent('text/filter', [], $html);

            return $html;
        }
    }
}