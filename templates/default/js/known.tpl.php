<?php

/* 
 * Constructs a copy of the Known environment and make it available to javascript.
 * 
 * @todo: Add more information as necessary
 * @todo: Consider doing this via ajax.
 */

/**
 * Environment array. 
 * Later json_encoded to ensure it's natively escaped.
 */
$known = [
    'session' => [
        'loggedIn' => \Idno\Core\Idno::site()->session()->isLoggedIn(),
    ],
    'config' => [
        'displayUrl' => \Idno\Core\Idno::site()->config()->getDisplayURL()
    ]
];

?>
<script>
    var known = <?= json_encode($known); ?>;
    
</script>