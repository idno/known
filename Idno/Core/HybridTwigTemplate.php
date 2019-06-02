<?php

/**
 * Hybrid twig template management class
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {
    
    use Twig\Loader\FilesystemLoader;
    use Twig\Environment;
    
    /**
     * Hybrid twig template management class.
     * 
     * This class extends the Known template to, in addition to supporting the Known php based templates, to support
     * the more standard twig templates.
     */
    class HybridTwigTemplate extends Template {
        
        private $twig;
        
        private $loader;
        
        public function __construct($template = false) {
            
            // Set up twig environment
            $this->loader = new FilesystemLoader(\Idno\Core\Bonita\Main::getPaths());
            $this->twig = new Environment($this->loader);
            
            // Update with the parent
            return parent::__construct($template);
        }
        
        /**
         * Extension-aware version of the template drawing function
         *
         * @param string $templateName
         * @param bool $returnBlank Should we return a blank string if the template doesn't exist? (Defaults to true)
         * @param book $replacements Should we honor template replacements? (Defaults to true)
         * @return false|string
         */
        function draw($templateName, $returnBlank = true, $replacements = true)
        {
            $result = '';
            if (!empty($this->prepends[$templateName])) {
                foreach ($this->prepends[$templateName] as $template) {
                    try {
                        $result .= $this->twig->render("{$template}.twig", $this->vars);
                    } catch (\Exception $e) {
                        // Ignore loading errors here
                    }
                    
                }
            }
            $replaced = false;
            foreach(['*', $this->getTemplateType()] as $templateType) {
                if (!empty($this->replacements[$templateName][$templateType]) && $replacements == true) {
                    try {
                        $result .= $this->twig->render("{$this->replacements[$templateName][$templateType]}.twig", $this->vars);
                    
                        $replaced = true;
                    } catch (\Exception $e) {
                        // Ignore loading errors here
                    }
                }
                if (!empty($this->extensions[$templateName][$templateType])) {
                    foreach ($this->extensions[$templateName][$templateType] as $template) {
                        try {
                            $result .= $this->twig->render("{$template}.twig", $this->vars);
                        } catch (\Exception $e) {
                            // Ignore loading errors here
                        }
                    }
                }
            }
            if (!$replaced) {
                try {
                    $result .= $this->twig->render("{$templateName}.twig", $this->vars);
                } catch (\Exception $e) {
                    // Ignore loading errors here
                }
            }
            
            return parent::draw($templateName, $returnBlank, $replacements);
        }
    }
    
}