<?php

namespace ConsolePlugins\Export {
    class Main extends \Idno\Common\ConsolePlugin
    {

        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
            // Get all the registered content types
            $types = \Idno\Common\ContentType::getRegisteredClasses();

            // Get all posts
            $posts = \Idno\Common\Entity::getFromX($types, [], [], PHP_INT_MAX);
            foreach($posts as $post ) {
                $output->writeln($post->getTitle());
            }
        }

        public function getCommand()
        {
            return 'ghost-export';
        }

        public function getDescription()
        {
            return \Idno\Core\Idno::site()->language()->_('Exports Known posts to Ghost\'s Lexical JSON format.');
        }

        public function getParameters()
        {
            return [
                //new \Symfony\Component\Console\Input\InputArgument('echo', \Symfony\Component\Console\Input\InputArgument::REQUIRED, \Idno\Core\Idno::site()->language()->_('Text to echo'))
            ];
        }

    }
}
