#!/usr/bin/php -q

<?php

    // Load Symfony
    require_once((dirname(__FILE__)) . '/external/Symfony/Component/ClassLoader/UniversalClassLoader.php');
    $known_loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
    $known_loader->registerNamespace('Symfony\Component', (dirname(__FILE__)) . '/external');
    $known_loader->register();

    // Register console namespace
    use Symfony\Component\Console\Application;

    // Create new console application
    $console = new Application();

    // Load any plugin functions
    $directory = dirname(__FILE__) . '/ConsolePlugins/';
    if ($scanned_directory = array_diff(scandir($directory), array('..', '.'))) {
        foreach ($scanned_directory as $file) {
            if (is_dir($file)) {
                if (file_exists($directory . $file . '/Main.php')) {
                    @include($directory . $file . '/Main.php');
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
        });

    // Run the application
    $console->run();