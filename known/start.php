<?php

    /**
     * known loader and all-purpose conductor
     *
     * @package known
     * @subpackage core
     */

// We're making heavy use of the Symfony ClassLoader to load our classes
    require_once(dirname(dirname(__FILE__)) . '/external/Symfony/Component/ClassLoader/UniversalClassLoader.php');
    $loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();

// Register our main namespaces (all known classes adhere to the PSR-0 standard)

// known trunk classes (i.e., the main framework) are in /known
    $loader->registerNamespace('known', dirname(dirname(__FILE__)));
// known plugins are located in /knownPlugins and must have their own namespace
    $loader->registerNamespace('knownPlugins', dirname(dirname(__FILE__)));

// Register our external namespaces (PSR-0 compliant modules that we love, trust and need)

// Bonita is being used for templating
    $loader->registerNamespace('Bonita', dirname(dirname(__FILE__)) . '/external/bonita/includes');
// Symfony is used for routing, observer design pattern support, and a bunch of other fun stuff
    $loader->registerNamespace('Symfony\Component', dirname(dirname(__FILE__)) . '/external');

// Using Toro for URL routing
    require_once(dirname(dirname(__FILE__)) . '/external/torophp/src/Toro.php');

// Using mf2 for microformats parsing, and webignition components to support it
    $loader->registerNamespace('webignition\Url', dirname(dirname(__FILE__)) . '/external/webignition/url/src');
    $loader->registerNamespace('webignition\AbsoluteUrlDeriver', dirname(dirname(__FILE__)) . '/external/webignition/absolute-url-deriver/src');
    $loader->registerNamespace('webignition\NormalisedUrl', dirname(dirname(__FILE__)) . '/external/webignition/url/src');
    $loader->registerNamespace('Mf2', dirname(dirname(__FILE__)) . '/external/mf2');

// Register the autoloader
    $loader->register();

// Register the known-templates folder as the place to look for templates in Bonita
    \Bonita\Main::additionalPath(dirname(dirname(__FILE__)));

// Not sure if this is the way we should be initializing everything yet.
// TODO: do this more intelligently.

    $known       = new known\Core\known();
    $account    = new known\Core\Account();
    $admin      = new known\Core\Admin();
    $webfinger  = new known\Core\Webfinger();
    $webmention = new known\Core\Webmention();