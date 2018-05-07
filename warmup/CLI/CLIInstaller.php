#!/usr/bin/php -q
<?php

/**
 * Load Idno
 */
spl_autoload_register(function($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $basedir = dirname(dirname(dirname(__FILE__))) . '/';
    if (file_exists($basedir . $class . '.php')) {
        include_once($basedir . $class . '.php');
    }
});

/**
 * Load Symfony
 */
spl_autoload_register(function($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $basedir = dirname(dirname(dirname(__FILE__))) . '/external/';
    if (file_exists($basedir . $class . '.php')) {
        include_once($basedir . $class . '.php');
    }
});

class CLIInstaller extends \Idno\Core\Installer {
    
    private $application;
    private $config = [];
    private $expected_manifest = [
        'site_title' => '"Site title"',
        'mysql_name' => '"Database name"',
        'mysql_user' => '"Database username"',
        'mysql_pass' => '"Database password"',
        'mysql_host' => '"Database host (usually \'localhost\')"',
        'upload_path' => '"Upload path"',
    ];
    
    public function __construct() {
        $this->application = new \Symfony\Component\Console\Application('Known Console Installer', \Idno\Core\Version::version());
        
        parent::__construct();
    }
        
    public function run() {

        $this->application
            ->register('generate-manifest')
            ->setDescription('Generate a template install manifest')
            ->setDefinition([
                new \Symfony\Component\Console\Input\InputArgument('manifest', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'File to write the template to'),
            ])
            ->setCode(function (\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
                
                if ($fp = fopen($input->getArgument('manifest'), 'w')) {

                    foreach($this->expected_manifest as $key => $value) {
                        fwrite($fp, "$key = $value\n");
                    }
                    fclose($fp);

                } else {

                    $output->writeln("Couldn't open " . $input->getArgument('filename'));

                }
            });
        
        $this->application
            ->register('install')
            ->setDescription('Install Known')
            ->setDefinition([
                new \Symfony\Component\Console\Input\InputArgument('config', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Specify the output config to write, this could be config.ini (default) or my.domain.ini for a domain specific config.', 'config.ini'),
                new \Symfony\Component\Console\Input\InputArgument('manifest', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Configuration manifest. If not provided, you will be prompted for settings.', ''),
            ])
            ->setCode(function (\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
                
                $helper = new Symfony\Component\Console\Helper\QuestionHelper();
                
                if ($this->isInstalled())
                    throw new \Exception("Known is already installed.");
                
                
                // Check requirements
                $output->writeln("Checking requirements...");
                
                $phpversion = Idno\Core\Installer::checkPHPVersion();
                if ($phpversion == 'ok') {
                    $output->writeln("\tYou are running PHP version " . phpversion() . '.');
                } else if ($phpversion == 'warn') {
                    $output->writeln("\tYou are running Known using a very old version of PHP (" . phpversion() . '), which is no longer actively supported. Although Known will currently still install, some features may not work, so you should upgrade soon. You may need to ask your server administrator to upgrade PHP for you.');
                } else {
                    throw new \Exception('You are running PHP version ' . phpversion() . ', which cannot run Known. You may need to ask your server administrator to upgrade PHP for you.');
                }
                
                
                if (function_exists('apache_get_modules')) {
                    if (Idno\Core\Installer::rewriteAvailable()) {
                        $output->writeln("\tmod_rewrite is installed and enabled.");
                    } else {
                        throw new \Exception('mod_rewrite is not installed. Known cannot process page URLs without it.');
                    }
                } else {
                    $output->writeln("\tWe couldn't detect if mod_rewrite was installed, probably because you're using the CLI installer. Known cannot process page URLs without it, so take care!");
                }
                
                $output->writeln("\tChecking extensions...");
                $extensions = Idno\Core\Installer::requiredModules();
                asort($extensions);
                foreach($extensions as $extension) {
                    if (extension_loaded($extension)) {
                        $output->writeln("\t\t$extension extension is installed.");
                    } else {
                        throw new \Exception("$extension extension is not installed.");
                        
                    }
                }
                
                $output->writeln(" ");
                
                // Load manifest if given
                if ($filename = $input->getArgument('manifest')) {
                    if (file_exists($filename)) {
                        $this->config = @parse_ini_file($filename);
                    }
                }
                
                // Load config name
                $config_name = $input->getArgument('config');
                if (empty($config_name)) {
                    $config_name = 'config.ini';
                }
                
                // Gather settings
                if (empty($this->config['site_title'])) {
                    $question = new Symfony\Component\Console\Question\Question('Please enter the name of the site: ');

                    $this->config['site_title'] = $helper->ask($input, $output, $question);
                }
                
                
                if (empty($this->config['mysql_name'])) {
                    $question = new Symfony\Component\Console\Question\Question('Please enter the name of the database: ', 'known');

                    $this->config['mysql_name'] = $helper->ask($input, $output, $question);
                }
                if (empty($this->config['mysql_user'])) {
                    $question = new Symfony\Component\Console\Question\Question('Please enter the username: ');

                    $this->config['mysql_user'] = $helper->ask($input, $output, $question);
                }
                if (empty($this->config['mysql_pass'])) {
                    $question = new Symfony\Component\Console\Question\Question('Please enter the database password: ');
                    $question->setHidden(true);

                    $this->config['mysql_pass'] = $helper->ask($input, $output, $question);
                }
                if (empty($this->config['mysql_host'])) {
                    $question = new Symfony\Component\Console\Question\Question('Please enter the database hostname: ', 'localhost');

                    $this->config['mysql_host'] = $helper->ask($input, $output, $question);
                }
                if (empty($this->config['upload_path'])) {
                    $question = new Symfony\Component\Console\Question\Question('Please enter the upload path: ', $this->root_path . '/Uploads/');

                    $this->config['upload_path'] = $helper->ask($input, $output, $question);
                    if (empty($this->config['upload_path']))
                        $this->config['upload_path'] = $this->root_path . '/Uploads/';
                }
                
                
                // Check upload path
                if (!empty($this->config['upload_path'])){
                    $this->checkUploadDirectory($this->config['upload_path']);
                }
                
                $this->installSchema(
                    $this->config['mysql_host'], 
                    $this->config['mysql_name'], 
                    $this->config['mysql_user'], 
                    $this->config['mysql_pass']
                );
                
                $this->writeApacheConfig();
                
                $ini_file = <<< END

# This configuration file was created by Known's installer.

database = 'MySQL'
dbname = '{$this->config['mysql_name']}'
dbpass = '{$this->config['mysql_pass']}'
dbuser = '{$this->config['mysql_user']}'
dbhost = '{$this->config['mysql_host']}'

filesystem = 'local'
uploadpath = '{$this->config['upload_path']}'

END;
            
                $this->writeConfig($ini_file, $config_name);
                
                
                $output->writeln("Your site should now be installed. Visit your site to create your first user!");
            });
            
        $this->application->run();
    }

}

$installer = new CLIInstaller();
$installer->run();