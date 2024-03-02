<?php

namespace Idno\Core {

    interface Template
    {

        /**
         * Override a page shell based on the page root.
         *
         * @param string $path_root Url base, e.g. 'settings'
         * @param string $shell     The shell, e.g. 'settings-shell'
         */
        public function addUrlShellOverride($path_root, $shell);

        /**
         * Extension-aware version of the template drawing function
         *
         * @param  string $templateName
         * @param  bool   $returnBlank  Should we return a blank string if the template doesn't exist? (Defaults to true)
         * @param  book   $replacements Should we honor template replacements? (Defaults to true)
         * @return \Idno\Core\Bonita\false|string
         */
        function draw($templateName, $returnBlank = true, $replacements = true);

        /**
         * Draws the page shell.
         *
         * @param  bool $echo
         * @param  $shell Optional override of the page shell template to be used
         * @return false|string
         */
        function drawPage($echo = true, $shell = 'shell');

        /**
         * Draw syndication buttons relating to a particular content type
         *
         * @param  $content_type
         * @param  $posse_links  containing Entity::getPosseLinks()
         * @return \Idno\Core\Bonita\false|string
         */
        function drawSyndication($content_type, $posse_links = []);

        /**
         * Draws generic pagination suitable for placing somewhere on a page (offset is drawn from the 'offset' input variable)
         *
         * @param  int   $count          Number of items in total (across all pages)
         * @param  int   $items_per_page Number of items you're displaying per page
         * @param  array $vars           Additional template variables
         * @return string
         */
        function drawPagination($count, $items_per_page = null, array $vars = []);

        /**
         * Extend a template with another template. eg, template "plugin/atemplate"
         * could extend "core/atemplate"; if this is the case, the results of
         * $template->draw('plugin/atemplate') will be automatically appended to
         * the end of the results of $template->draw('core/atemplate').
         *
         * @param string $templateName
         * @param string $extensionTemplateName
         * @param bool   $to_front              If set, this will add the template to the beginning of the template queue
         */
        function extendTemplate($templateName, $extensionTemplateName, $to_front = false, $templateType = '*');

        /**
         * Prepend a template with another template. eg, template "plugin/atemplate"
         * could extend "core/atemplate"; if this is the case, the results of
         * $template->draw('plugin/atemplate') will be automatically prepended to
         * the end of the results of $template->draw('core/atemplate').
         *
         * @param string $templateName
         * @param string $prependTemplateName
         * @param bool   $to_front            If set, this will add the template to the beginning of the template queue
         */
        function prependTemplate($templateName, $prependTemplateName, $to_front = false);

        /**
         * Replace a core template with another template. eg, template "plugin/atemplate"
         * could replace "core/atemplate"; if this is the case, the results of
         * $template->draw('plugin/atemplate') will be displayed instead of
         * $template->draw('core/atemplate'). Usually this isn't required - Known replaces
         * templates automatically if you create one in your plugin with the same name -
         * but this function enables conditional replacements.
         *
         * @param string $templateName
         * @param string $extensionTemplateName
         */
        function replaceTemplate($templateName, $replacementTemplateName, $templateType = '*');

        /**
         * Extends a given template with pre-rendered content. All pre-rendered content will be drawn after
         * template-driven content.
         *
         * @param $templateName
         * @param $content
         */
        function extendTemplateWithContent($templateName, $content);

        /**
         * Sets the current template type
         *
         * @param string $template The name of the template you wish to use
         */
        function setTemplateType($templateType);

        /**
         * Sets the template type based on various environmental factors
         */
        function autodetectTemplateType();

    }

}