#!/usr/bin/php -q
<?php

    define('KNOWN_CONSOLE', 'true');

    // Load Symfony
    require_once((dirname(__FILE__)) . '/external/Symfony/Component/ClassLoader/UniversalClassLoader.php');
    global $known_loader;
    $known_loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
    $known_loader->registerNamespace('Symfony\Component', (dirname(__FILE__)) . '/external');
    $known_loader->register();
    
    /**
     * Retrieve the loader
     * @return \Symfony\Component\ClassLoader\UniversalClassLoader
     */
    function &loader()
    {
        global $known_loader;

        return $known_loader;
    }

    // Register console namespace
    use Symfony\Component\Console\Application;


    // Known core namespaces
    $known_loader->registerNamespace('Idno', dirname(__FILE__) );
    $known_loader->registerNamespace('ConsolePlugins',  dirname(__FILE__) );
    
    // Implement the PSR-3 logging interface
    $known_loader->registerNamespace('Psr\Log', dirname(__FILE__) . '/external/log');
    
    // Symfony is used for routing, observer design pattern support, and a bunch of other fun stuff
    $known_loader->registerNamespace('Symfony\Component', dirname(__FILE__) . '/external');
    
    $known_loader->registerNamespace('webignition\Url', dirname(__FILE__) . '/external/webignition/url/src');
    $known_loader->registerNamespace('webignition\AbsoluteUrlDeriver', dirname(__FILE__) . '/external/webignition/absolute-url-deriver/src');
    $known_loader->registerNamespace('webignition\NormalisedUrl', dirname(__FILE__) . '/external/webignition/url/src');
    $known_loader->registerNamespace('Mf2', dirname(__FILE__) . '/external/mf2');
    $known_loader->registerNamespace('IndieWeb', dirname(__FILE__) . '/external/mention-client-php/src');
    
    
    // TODO: FIND A WAY TO NOT LOAD THESE FOR CONSOLE
    
    // Using HTMLPurifier for HTML sanitization
    include dirname(__FILE__) . '/external/htmlpurifier-lite/library/HTMLPurifier.auto.php';
    ///////////////////
    
    
    // Create new console application
    global $console;
    $console = new Application('Known Console', \Idno\Core\Version::version());
    
    function &application() {
        global $console;
        
        return $console;
    }
    
    // Load any plugin functions
    $directory = dirname(__FILE__) . '/ConsolePlugins/';
    if ($scanned_directory = array_diff(scandir($directory), array('..', '.'))) {
        foreach ($scanned_directory as $file) {
            if (is_dir($directory . $file)) {
                $class = "ConsolePlugins\\$file\\Main"; 
                if (class_exists($class)) {
                    $c = new $class(); 
                    if ($c instanceof \Idno\Common\ConsolePlugin) {
                        $console->register($c->getCommand())
                                ->setDescription($c->getDescription())
                                ->setDefinition($c->getParameters())
                                ->setCode([$c, 'execute']);
                    } 
                }
            } else {
                $stubclass = "ConsolePlugins\\$file";
                $stubclass = str_replace('.php', '', $stubclass);
                if (class_exists($stubclass)) {
                    $c = new $stubclass(); 
                    if ($c instanceof \Idno\Common\ConsolePlugin) {
                        $console->register($c->getCommand())
                                ->setDescription($c->getDescription())
                                ->setDefinition($c->getParameters())
                                ->setCode([$c, 'execute']);
                    } 
                }
            }
        }
    }
    
    // Allow regular plugins to contain a ConsoleMain.php
    $directory = dirname(__FILE__) . '/IdnoPlugins/';
    if ($scanned_directory = array_diff(scandir($directory), array('..', '.'))) {
        foreach ($scanned_directory as $file) {
            if (is_dir($directory . $file)) {
                $class = "IdnoPlugins\\$file\\ConsoleMain"; 
                if (class_exists($class)) {
                    $c = new $class(); 
                    if ($c instanceof \Idno\Common\ConsolePlugin) {
                        $console->register($c->getCommand())
                                ->setDescription($c->getDescription())
                                ->setDefinition($c->getParameters())
                                ->setCode([$c, 'execute']);
                    } 
                }
                
                // See if there's a console subdir, for multiple sub commands
                $scanned = @scandir($directory . $file . '/Console/');
                if (!empty($scanned) && $scanned_sub_directory = array_diff($scanned, array('..', '.'))) {
                    foreach ($scanned_sub_directory as $file2) {
                        $class2 = "IdnoPlugins\\$file\\Console\\$file2";                        
                        $class2 = str_replace('.php', '', $class2);
                        if (class_exists($class2)) {
                            $c = new $class2(); 
                            if ($c instanceof \Idno\Common\ConsolePlugin) {
                                $console->register($c->getCommand())
                                        ->setDescription($c->getDescription())
                                        ->setDefinition($c->getParameters())
                                        ->setCode([$c, 'execute']);
                            } 
                        }
                    }
                }
            }
        }
    }

    $console
        ->register('makeconfig')
        ->setDescription('Attempts to write configuration variables to a Known config.ini file')
        ->setDefinition([
            new \Symfony\Component\Console\Input\InputArgument('dbuser', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Database username'),
            new \Symfony\Component\Console\Input\InputArgument('dbpass', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Database password'),
            new \Symfony\Component\Console\Input\InputArgument('dbname', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Database name'),
            new \Symfony\Component\Console\Input\InputArgument('database', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Database type', 'mysql'),
            new \Symfony\Component\Console\Input\InputArgument('dbhost', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Database hostname', 'localhost'),
            new \Symfony\Component\Console\Input\InputArgument('filename', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Configuration filename', 'config.ini'),
        ])
        ->setCode(function (\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
            if ($fp = fopen($input->getArgument('filename'), 'w')) {

                fwrite($fp, "[Database configuration]\n");
                fwrite($fp, "database=" . $input->getArgument('database') . "\n");
                fwrite($fp, "dbhost=" . $input->getArgument('dbhost') . "\n");
                fwrite($fp, "dbname=" . $input->getArgument('dbname') . "\n");
                fwrite($fp, "dbuser=" . $input->getArgument('dbuser') . "\n");
                fwrite($fp, "dbpass=" . $input->getArgument('dbpass') . "\n");
                fclose($fp);

            } else {

                $output->writeln("Couldn't open " . $input->getArgument('filename'));

            }
        });

    $console
        ->register('version')
        ->setDescription('Returns the current Known version as defined in version.known')
        ->setDefinition([])
        ->setCode(function (\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
            $output->writeln(file_get_contents(dirname(__FILE__) . '/version.known'));
            
            $remoteVersion = \Idno\Core\RemoteVersion::build();
            if (\Idno\Core\Version::build() < $remoteVersion) {
                $version = \Idno\Core\RemoteVersion::version();
                $output->writeln("WARNING: Your build of Known is behind the latest version from Github ($version - $remoteVersion). If you're having problems, you may want to try updating to the latest version.\nUpdate now: https://github.com/idno/Known\n");
            }
        });

    // Boot known
        try {
            $idno         = new Idno\Core\Idno();
            $account      = new Idno\Core\Account();
            $admin        = new Idno\Core\Admin();
            $webfinger    = new Idno\Core\Webfinger();
            $webmention   = new Idno\Core\Webmention();
            $pubsubhubbub = new Idno\Core\PubSubHubbub();
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        
    // Run the application
    $console->run();