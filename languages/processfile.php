<?php

$in = file_get_contents("php://stdin");
$filenames = explode("\n", $in);
$handled = [];

function getLineNumber($content, $charpos) {
    list($before) = str_split($content, $charpos); // fetches all the text before the match

    return strlen($before) - strlen(str_replace("\n", "", $before)) + 1;
}

foreach ($filenames as $filename) {
    
    $file = @file_get_contents($filename);

    if (!empty($file)) {
        if (preg_match_all('/_\((\'|")(.*)(\'|")(\)|,)/imsU', $file, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[2] as $translation) {

                $string = $translation[0];
                $offset = $translation[1];
                $linenumber = getLineNumber($in, $offset);

                $normalised_string = str_replace('"', '\"', $string);

                if (!in_array($normalised_string, $handled)) {
                    echo "#: $filename:$linenumber\n";
                    echo "msgid \"$normalised_string\"\n";
                    echo "msgstr \"\"\n\n";
                    $handled[] = $normalised_string;
                }
            }
        }
    }
}