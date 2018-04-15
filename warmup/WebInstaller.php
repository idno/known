<?php

/**
 * Web based installer
 */
class WebInstaller extends \Idno\Core\Installer {
    
    private static $installer;
    
    private $template;    
    private $ssl_required = false;
    
    public function __construct() {
        \Idno\Core\Bonita\Main::additionalPath(dirname(__FILE__));
        $this->template = new \Idno\Core\Bonita\Templates(); // Use basic template here
        $this->template->setTemplateType('default');
        
        parent::__construct();
    }
        
    public function rewriteWorking() {
        if (!empty($_SERVER['PHP_SELF'])) {
            if ($subdir = dirname(dirname($_SERVER['PHP_SELF']))) {
                if ($subdir != DIRECTORY_SEPARATOR) {
                    if(substr($subdir, -1) == DIRECTORY_SEPARATOR) {
                        $subdir = substr($subdir, 0, -1);
                    }
                    if (substr($subdir, 0, 1) == DIRECTORY_SEPARATOR) {
                        $subdir = substr($subdir, 1);
                    }
                    $subdir = str_replace(DIRECTORY_SEPARATOR, '/', $subdir);
                }
            }
        }
        if (empty($subdir)) {
            $subdir = '';
        } else {
            $subdir = '/' . $subdir;
        }
        
        if (function_exists('apache_get_version')) {
            $host = strtolower($_SERVER['HTTP_HOST']);
            if (!empty(Idno\Common\Page::isSSL())) {
                $schema = 'https://';
            } else {
                $schema = 'http://';
            }

            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $schema . $host . $subdir . '/js/canary.js');
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_HEADER, 1);

            $curl_result = curl_exec($curl_handle);
            $curl_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

            if ($curl_status < 200 || $curl_status > 299) {
                return false;
            }

            curl_close($curl_handle);
            
            return true;
        }
    }
    
    protected function pageSettings() {
        $template = $this->template;
        $ok = true;
        $messages = '';
        
        $site_title  = \Idno\Core\Input::getInput('site_title');
        $mysql_user  = \Idno\Core\Input::getInput('mysql_user');
        $mysql_host  = \Idno\Core\Input::getInput('mysql_host');
        $mysql_pass  = \Idno\Core\Input::getInput('mysql_pass');
        $mysql_name  = \Idno\Core\Input::getInput('mysql_name');
        $upload_path = \Idno\Core\Input::getInput('upload_path', dirname(dirname(__FILE__)) . '/Uploads/');
               
        
        if (!WebInstaller::installer()->rewriteWorking()) {    
            $messages .= '<p>Rewriting appears to be disabled. Usually this means "AllowOverride None" is set in apache2.conf ';
            $messages .= 'which prevents Known\'s .htaccess from doing its thing. We tried to fetch a URL that should redirect ';
            $messages .= 'to default.js</p>';
            $messages .= '<p>You can usually fix this by setting <code>AllowOverride All</code> in your Apache configuration.</p>';
            $ok = false;
        }

        if (!empty($mysql_name) && !empty($mysql_host)) {
            try {
                $this->installSchema($mysql_host, $mysql_name, $mysql_user, $mysql_pass);
            } catch (Exception $e) {
                $messages .= '<p>We couldn\'t connect to your database. Please check your settings and try again. Here\'s the error we got:</p>';
                $messages .= '<blockquote><p>' . $e->getMessage() . '</p></blockquote>';
                $ok = false;
            }
        }

        $upload_path = realpath($upload_path);
        if (!empty($upload_path)) {
            if (substr($upload_path, -1) != '/' && substr($upload_path, -1) != '\\') {
                $upload_path .= '/';
            }
            
            try {
                $this->checkUploadDirectory($upload_path);
            } catch (\Exception $e) {
                $ok = false;
                $messages .= '<p>' . $e->getMessage() . '</p>';
            }
            
            
        } else {
            $ok = false;
            if (!empty($mysql_user)) {
                $messages .= '<p>You need to specify an upload path.</p>';
            }
        }
        
        if ($ok && !empty($upload_path) && !empty($mysql_name) && !empty($mysql_host)) {
            
            try {
                $this->writeApacheConfig();
            } catch (\Exception $e) {
                $ok = false;
                $messages .= '<p>' . $e->getMessage() . '</p>';
            }
        
            try {

                $ini_file = <<< END

# This configuration file was created by Known's installer.

database = 'MySQL'
dbname = '{$mysql_name}'
dbpass = '{$mysql_pass}'
dbuser = '{$mysql_user}'
dbhost = '{$mysql_host}'

filesystem = 'local'
uploadpath = '{$upload_path}'

END;
            
                $this->writeConfig($ini_file);

            } catch (\Exception $ex) {
                $ok = false;

                $template->__([
                    'title' => 'Save configuration file',
                    'body' => $template->__([
                        'ini_file' => $ini_file,

                    ])->draw('pages/write-config'),

                ])->drawPage();
            }
        }
        
        if ($ok) {
            if (WebInstaller::installer()->isInstalled()) {
                header('Location: ../begin/register?set_name=' . urlencode($site_title));
                exit;
            }
        }
        
        
        $template->__([
            'title' => 'Settings',
            'body' => $template->__([
                
                'site_title' => $site_title,
                'mysql_user' => $mysql_user,
                'mysql_host' => $mysql_host,
                'mysql_pass' => $mysql_pass,
                'mysql_name' => $mysql_name,
                'upload_path' => $upload_path,
                
                'messages' => $messages,
                
            ])->draw('pages/settings'),

        ])->drawPage();
    }

    public function run() {
        
        // See if we've installed things already
        if ($this->isInstalled()) {
            header('Location: ../'); exit;
        }
        
        $template = $this->template;
        
        switch (\Idno\Core\Input::getInput('stage')) {
         
            case 'settings' :
                $this->pageSettings();
                break;
            case 'requirements' :
                $template->__([
                    'title' => 'Requirements',
                    'body' => $template->__([
                        'ssl-required' => $this->ssl_required
                    ])->draw('pages/requirements'),
                    
                ])->drawPage();
                break;
            
            // Welcome message
            default:
                $template->__([
                    'body' => $template->draw('pages/begin')
                ])->drawPage();
        }
    }

    
    /**
     * Return the current installer
     * @return WebInstaller
     */
    public static function installer() {
        
        if (!empty(self::$installer))
            return self::$installer;
        
        self::$installer = new WebInstaller();
        
        return self::$installer;
    }
}
