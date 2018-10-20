<?php

namespace ConsolePlugins\Import {

    use Idno\Core\Migration;

    class Main extends \Idno\Common\ConsolePlugin
    {

        public static $run = true;

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'import', dirname(__FILE__) . '/languages/'
                )
            );
        }

        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
            $filename = $input->getArgument('file');
            $import_type = $input->getArgument('format');

            if (!file_exists($filename))
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Import file %s could not be found.', [$filename]));

            $xml = file_get_contents($filename);

            $imported = false;
            switch (strtolower($import_type)) {

                case 'blogger':
                    $imported = Migration::importBloggerXML($xml);
                    break;
                case 'wordpress':
                    $imported = Migration::importWordPressXML($xml);
                    break;
                default:
                    throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('%s is an unrecognised import type', [$import_type]));

            }

            if ($imported) {

                $output->writeln(\Idno\Core\Idno::site()->language()->_('Completed import successfully'));

            } else {

                $output->writeln(\Idno\Core\Idno::site()->language()->_('Import completed, but may not have been successful'));

            }
        }

        public function getCommand()
        {
            return 'import';
        }

        public function getDescription()
        {
            return \Idno\Core\Idno::site()->language()->_('Import posts from WordPress, Blogger etc');
        }

        public function getParameters()
        {
            return [
                new \Symfony\Component\Console\Input\InputArgument('format', \Symfony\Component\Console\Input\InputArgument::REQUIRED, \Idno\Core\Idno::site()->language()->_('Import type: wordpress, blogger')),
                new \Symfony\Component\Console\Input\InputArgument('file', \Symfony\Component\Console\Input\InputArgument::REQUIRED, \Idno\Core\Idno::site()->language()->_('Location of the export file/directory')),
            ];
        }

    }

}
