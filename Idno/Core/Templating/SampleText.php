<?php


namespace Idno\Core\Templating {

    trait SampleText
    {

        /**
         * Given HTML text, attempts to return text from the first $paras paragraphs
         *
         * @param  $html_text
         * @param  int $paras     Number of paragraphs to return; defaults to 1
         * @return string
         */
        function sampleParagraph($html_text, $paras = 1)
        {
            $sample = '';
            $dom    = new \DOMDocument;
            $dom->loadHTML($html_text);
            if ($p = $dom->getElementsByTagName('p')) {
                for ($i = 0; $i < $paras; $i++) {
                    $sample .= $p->item($i)->textContent;
                }
            }

            return $sample;
        }

        /**
         * Returns a snippet of plain text
         *
         * @param  $text
         * @param  int $words
         * @return array|string
         */
        function sampleText($text, $words = 32)
        {
            $formatted_text = trim(strip_tags($text));
            $formatted_text = explode(' ', $formatted_text);
            $formatted_text = array_slice($formatted_text, 0, $words);
            $formatted_text = implode(' ', $formatted_text);
            if (strlen($formatted_text) < strlen($text)) { $formatted_text .= ' ...';
            }
            return $formatted_text;
        }

        /**
         * Return a snippet of plain text based on a number of characters.
         *
         * @param string $text
         * @param int $chars
         */
        function sampleTextChars($text, $chars = 250, $dots = '...')
        {
            $text = trim(strip_tags($text));
            $length = strlen($text);

            // Short circuit if number of text is less than max chars
            if ($length <= $chars) {
                return $text;
            }

            $formatted_text = substr($text, 0, $chars);
            $space = strrpos($formatted_text, ' ', 0);

            // No space, don't crop
            if ($space === false) {
                $space = $chars;
            }

            $formatted_text = trim(substr($formatted_text, 0, $space));

            if ($length != strlen($formatted_text)) {
                $formatted_text .= $dots;
            }

            return $formatted_text;
        }

    }
}