<?php

namespace ConsolePlugins\Example {
    class Main extends \Idno\Common\ConsolePlugin
    {

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'example', dirname(__FILE__) . '/languages/'
                )
            );
        }

        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
             $output->writeln(\Idno\Core\Idno::site()->language()->_("You said... %s", [$input->getArgument('echo')]));
        }

        public function getCommand()
        {
            return 'example';
        }

        public function getDescription()
        {
            return \Idno\Core\Idno::site()->language()->_('Example plugin to show how to use the console plugin interface');
        }

        public function getParameters()
        {
            return [
                new \Symfony\Component\Console\Input\InputArgument('echo', \Symfony\Component\Console\Input\InputArgument::REQUIRED, \Idno\Core\Idno::site()->language()->_('Text to echo'))
            ];
        }

    }
}
