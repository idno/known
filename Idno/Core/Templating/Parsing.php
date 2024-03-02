<?php

namespace Idno\Core\Templating {

    use Idno\Entities\User;

    trait Parsing
    {


        /**
         * Automatically links URLs embedded in a piece of text
         *
         * @param  stirng $text
         * @param  string $code Optionally, code to inject into the anchor tag (eg to add classes). '%URL%' is replaced with the URL. Default: blank.
         * @return string
         */
        function parseURLs($text, $code = '')
        {
            $r = preg_replace_callback(
                '/(?<!=)(?<!["\'])((ht|f)tps?:\/\/[^\s<>"\']+)/i', function ($matches) use ($code) {
                    $url  = $matches[1];
                    $punc = '';

                    while ($url) {
                        $last = substr($url, -1, 1);
                        if (strstr('.!?,;:(', $last)
                            // strip ) if there isn't a matching ( earlier in the url
                            || ($last === ')' && !strstr($url, '('))
                        ) {
                            $punc = $last . $punc;
                            $url  = substr($url, 0, -1);
                        } else {
                            break; // found a non-punctuation character
                        }
                    }

                    $result = "<a href=\"" . $url ."\"";
                    if (!\Idno\Common\Entity::isLocalUUID($url)) {
                        $result .= " target=\"_blank\" ";
                    }
                    if ($code) {
                        $result .= ' ' . str_replace("%URL%", $url, $code);
                    }
                    $result .= ">";
                    $result .= preg_replace('/([\/=]+)/', '${1}<wbr />', static::sampleTextChars($url, 100));
                    $result .= "</a>$punc";

                    return $result;

                }, $text
            );

            return $r;
        }

        /**
         * Link any hashtags in the text
         *
         * @param  $text
         * @return string
         */
        function parseHashtags($text)
        {
            //decode &auml; to ä, but keep < > and & characters
            $text = html_entity_decode(
                str_replace(
                    ['&amp;', '&lt;', '&gt;'],
                    ['&amp;amp;', '&amp;lt;', '&amp;gt;'],
                    $text
                )
            );
            $r    = preg_replace_callback(
                '/(?<=^|[\>\s\n])(\#[\p{L}0-9\_]+)/u', function ($matches) {
                    $url = $matches[1];
                    $tag = str_replace('#', '', $matches[1]);

                    if (preg_match('/\#[0-9]{1,3}$/', $matches[1])) {
                        return $matches[1];
                    }

                    if (preg_match('/\#[A-Fa-f0-9]{6}$/', $matches[1])) {
                        return $matches[1];
                    }

                    return '<a href="' . \Idno\Core\Idno::site()->config()->getDisplayURL() . 'tag/' . urlencode($tag) . '" class="p-category" rel="tag">' . $url . '</a>';
                }, $text
            );

            return $r;
        }

        /**
         * Change @user links into active users.
         *
         * @param string $text        The text to parse
         * @param string|array $in_reply_to If specified, the function will make a (hopefully) sensible guess as to where the user is located
         */
        function parseUsers($text, $in_reply_to = null)
        {

            $usermatch_regex = '/(?<=^|[\>\s\n\.])(\@[\w0-9\_]+)/i';
            $r = $text;

            if (!empty($in_reply_to)) {

                // TODO: do this in a more pluggable way

                // It is only safe to make assumptions on @users if only one reply to is given
                if (!is_array($in_reply_to) || (is_array($in_reply_to) && count($in_reply_to) == 1)) {

                    if (is_array($in_reply_to)) {
                        $in_reply_to = $in_reply_to[0];
                    }

                    $r = preg_replace_callback(
                        $usermatch_regex, function ($matches) use ($in_reply_to) {
                            $url = $matches[1];

                            // Find and replace twitter
                            if (strpos($in_reply_to, 'twitter.com') !== false) {
                                return '<a href="https://twitter.com/' . urlencode(ltrim($matches[1], '@')) . '" target="_blank">' . $url . '</a>';
                                // Activate github
                            } else if (strpos($in_reply_to, 'github.com') !== false) {
                                return '<a href="https://github.com/' . urlencode(ltrim($matches[1], '@')) . '" target="_blank">' . $url . '</a>';
                            } else {
                                return \Idno\Core\Idno::site()->events()->triggerEvent(
                                    "template/parseusers", [
                                    'in_reply_to' => $in_reply_to,
                                    'in_reply_to_domain' => parse_url($in_reply_to, PHP_URL_HOST),
                                    'username' => ltrim($matches[1], '@'),
                                    'match' => $url
                                    ], $url
                                );
                            }
                        }, $text
                    );

                }

            } else {
                // No in-reply, so we assume a local user
                $r = preg_replace_callback(
                    $usermatch_regex, function ($matches) {
                        $url = $matches[1];

                        $username = ltrim($matches[1], '@');

                        if ($user = User::getByHandle($username)) {
                            return '<a href="' . \Idno\Core\Idno::site()->config()->url . 'profile/' . urlencode($username) . '" >' . $url . '</a>';
                        } else {
                            return $url;
                        }

                    }, $text
                );
            }

            return $r;
        }


    }
}