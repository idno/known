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
        'admin' => \Idno\Core\Idno::site()->session()->isAdmin()
    ],
    'config' => [
        'displayUrl' => \Idno\Core\Idno::site()->config()->getDisplayURL(),
        'staticUrl' => \Idno\Core\Idno::site()->config()->getStaticURL(),
        'debug' => !empty(\Idno\Core\Idno::site()->config()->debug)
    ],
    'page' => [
        'currentUrl' => \Idno\Core\Idno::site()->currentPage()->currentUrl(),
        'currentUrlFragments' => \Idno\Core\Idno::site()->currentPage()->currentUrl(true),
    ],
];

?>
<script>
    var known = <?php echo json_encode($known); ?>; 
</script>