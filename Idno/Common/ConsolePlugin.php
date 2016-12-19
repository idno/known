<?php

/**
 * All known console plugins should extend this component.
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Common {

    abstract class ConsolePlugin extends Plugin {

        protected $console;

        function __construct() {

            $this->console = application();

            $this->init();
            $this->registerEventHooks();
        }
        
        function init() {}
        
        /**
         * Return a string defining the command name to execute.
         */
        abstract function getCommand();
        /**
         * Return a description of the command.
         */
        abstract function getDescription();
        /**
         * Return an array of \Symfony\Component\Console\Input\InputArgument defining the parameters to use.
         */
        abstract function getParameters();
        
        /**
         * Execute the command
         */
        abstract function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output);

    }

}
