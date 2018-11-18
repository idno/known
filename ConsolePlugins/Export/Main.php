<?php

namespace ConsolePlugins\Export {

    use Idno\Core\Migration;

    class Main extends \Idno\Common\ConsolePlugin
    {

        public static $run = true;

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'export', dirname(__FILE__) . '/languages/'
                )
            );
        }

        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
            //$directory = $input->getArgument('directory');
            $directory = false;
            
            \Idno\Core\Idno::site()->config()->export_last_requested = time();
            \Idno\Core\Idno::site()->config()->export_in_progress    = 1;
            \Idno\Core\Idno::site()->config()->save();
            
            // Remove the previous export file
            if (!empty(\Idno\Core\Idno::site()->config()->export_file_id)) {
                if ($file = File::getByID(\Idno\Core\Idno::site()->config()->export_file_id)) {
                    if ($file instanceof \MongoGridFSFile) {
                        // TODO: Handle this correctly
                    } else {
                        $file->remove();
                    }
                    \Idno\Core\Idno::site()->config()->export_file_id  = false;
                    \Idno\Core\Idno::site()->config()->export_filename = false;
                    \Idno\Core\Idno::site()->config()->save();
                }
            }
          
            if ($path = Migration::createCompressedArchive($directory)) {

                \Idno\Core\Idno::site()->config()->export_filename    = $filename;
                \Idno\Core\Idno::site()->config()->export_file_id     = $file;
                \Idno\Core\Idno::site()->config()->export_in_progress = 0;
                \Idno\Core\Idno::site()->config()->save();

                $output->writeln(\Idno\Core\Idno::site()->language()->_("Archive generated at %s", [$path]));
                
            } else {
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_("Export did not return an archive path."));
            }
            
        }

        public function getCommand()
        {
            return 'export';
        }

        public function getDescription()
        {
            return \Idno\Core\Idno::site()->language()->_('Export posts to a number of formats');
        }

        public function getParameters()
        {
            return [
               // new \Symfony\Component\Console\Input\InputArgument('directory', \Symfony\Component\Console\Input\InputArgument::REQUIRED, \Idno\Core\Idno::site()->language()->_('Location of the directory to export to')),
            ];
        }

    }

}
