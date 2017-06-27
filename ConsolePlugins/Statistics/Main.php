<?php

namespace ConsolePlugins\Statistics {

    class Main extends \Idno\Common\ConsolePlugin {

        protected function writeReport($output, $report, $level = 1) {

            foreach ($report as $label => $value) {

                if (is_array($value)) {

                    $title = "";
                    for ($n = 0; $n < $level; $n++)
                        $title .= "#";
                    $title .= " $label";

                    $output->writeln($label);
                    $output->writeln(str_pad("", strlen($label), '-'));

                    $this->writeReport($output, $value, $level + 1);

                    $output->writeln("");
                } else {

                    $output->writeln("$label: $value");
                }
            }
        }

        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
            $output->writeln("Gathering statistics for " . \Idno\Core\Idno::site()->config()->getURL());
            $output->writeln("");

            $report = \Idno\Core\Statistics::gather();

            $this->writeReport($output, $report);
        }

        public function getCommand() {
            return 'statistics';
        }

        public function getDescription() {
            return 'Retrieve admin statistics from your Known site';
        }

        public function getParameters() {
            return [
            ];
        }

    }

}