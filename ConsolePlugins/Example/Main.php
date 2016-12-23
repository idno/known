<?php

namespace ConsolePlugins\Example {
    class Main extends \Idno\Common\ConsolePlugin {
        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
             $output->writeln("You said... " . $input->getArgument('echo'));
        }

        public function getCommand() {
            return 'example';
        }

        public function getDescription() {
            return 'Example plugin to show how to use the console plugin interface';
        }

        public function getParameters() {
            return [
                new \Symfony\Component\Console\Input\InputArgument('echo', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Text to echo')
            ];
        }

    }
}