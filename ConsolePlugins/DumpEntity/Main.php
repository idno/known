<?php

namespace ConsolePlugins\DumpEntity {
    class Main extends \Idno\Common\ConsolePlugin {
        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
             
            $entity = \Idno\Common\Entity::getByID($input->getArgument('id'));
            if (!$entity) $entity = \Idno\Common\Entity::getByUUID ($input->getArgument('id'));
            if (!$entity) $entity = \Idno\Common\Entity::getByShortURL($input->getArgument('id'));
            if (!$entity) die("Error: Could not retrieve entity " . $input->getArgument('id'));
            
            
            print_r($entity);
        }

        public function getCommand() {
            return 'dump-entity';
        }

        public function getDescription() {
            return 'Dump an entity via its entity/UUID';
        }

        public function getParameters() {
            return [
                new \Symfony\Component\Console\Input\InputArgument('id', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Entity ID or UUID')
            ];
        }

    }
}