<?php

namespace ConsolePlugins\CronService {

    use Idno\Core\Idno;

    class Main extends \Idno\Common\ConsolePlugin
    {

        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
            $output->writeln('Starting periodic cron service...');
            $output->writeln('Press Ctrl+C to stop.');

            $lastMinute = null;
            $lastHour = null;
            $lastDay = null;

            while (true) {
                $currentMinute = date('Y-m-d H:i');
                $currentHour = date('Y-m-d H');
                $currentDay = date('Y-m-d');

                if ($currentMinute !== $lastMinute) {
                    $lastMinute = $currentMinute;
                    $output->writeln("[" . date('r') . "] Triggering cron/minute");
                    try {
                        Idno::site()->events()->triggerEvent('cron/minute');
                    } catch (\Exception $e) {
                        $output->writeln("<error>[" . date('r') . "] cron/minute error: " . $e->getMessage() . "</error>");
                    }
                }

                if ($currentHour !== $lastHour) {
                    $lastHour = $currentHour;
                    $output->writeln("[" . date('r') . "] Triggering cron/hourly");
                    try {
                        Idno::site()->events()->triggerEvent('cron/hourly');
                    } catch (\Exception $e) {
                        $output->writeln("<error>[" . date('r') . "] cron/hourly error: " . $e->getMessage() . "</error>");
                    }
                }

                if ($currentDay !== $lastDay) {
                    $lastDay = $currentDay;
                    $output->writeln("[" . date('r') . "] Triggering cron/daily");
                    try {
                        Idno::site()->events()->triggerEvent('cron/daily');
                    } catch (\Exception $e) {
                        $output->writeln("<error>[" . date('r') . "] cron/daily error: " . $e->getMessage() . "</error>");
                    }
                }

                sleep(30);
            }
        }

        public function getCommand()
        {
            return 'service-cron';
        }

        public function getDescription()
        {
            return 'Runs periodic cron events (cron/minute, cron/hourly, cron/daily)';
        }

        public function getParameters()
        {
            return [];
        }

    }
}
