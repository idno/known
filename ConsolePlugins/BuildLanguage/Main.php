<?php

namespace ConsolePlugins\BuildLanguage;

use Idno\Core\PotGenerator;

class Main extends \Idno\Common\ConsolePlugin
{

    public function getCommand(): string
    {
        return 'build-lang';
    }

    public function getDescription(): string
    {
        return 'Extract translatable strings from PHP files and generate .pot files';
    }

    public function getParameters(): array
    {
        return [
            new \Symfony\Component\Console\Input\InputArgument(
                'target',
                \Symfony\Component\Console\Input\InputArgument::OPTIONAL,
                'Target: "core", "plugin:Name", "theme:Name", or "all"',
                'core'
            ),
        ];
    }

    public function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): void {

        $target = $input->getArgument('target');
        $basePath = \Idno\Core\Idno::site()->config()->getPath();

        if ($target === 'all') {
            $this->buildAll($basePath, $output);
            return;
        }

        if ($target === 'core') {
            $this->buildCore($basePath, $output);
            return;
        }

        if (str_starts_with($target, 'plugin:')) {
            $name = substr($target, 7);
            $this->buildPlugin($basePath, $name, $output);
            return;
        }

        if (str_starts_with($target, 'theme:')) {
            $name = substr($target, 6);
            $this->buildTheme($basePath, $name, $output);
            return;
        }

        $output->writeln("<error>Unknown target: {$target}</error>");
        $output->writeln('Usage: build-lang [core|all|plugin:Name|theme:Name]');
    }

    private function buildCore(string $basePath, \Symfony\Component\Console\Output\OutputInterface $output): void
    {
        $generator = new PotGenerator();

        $dirs = [
            $basePath . '/Idno',
            $basePath . '/templates',
        ];

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $generator->extractFromDirectory($dir, $basePath);
            }
        }

        // Also extract from the console entry point
        $consoleFile = $basePath . '/idno.php';
        if (file_exists($consoleFile)) {
            $generator->extractFromFile($consoleFile, $basePath);
        }

        $outputDir = $basePath . '/languages/source';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $potFile = $outputDir . '/idno.pot';
        $content = $generator->generatePot('idno');
        file_put_contents($potFile, $content);

        $count = count($generator->getStrings());
        $output->writeln("Core: extracted {$count} strings -> languages/source/idno.pot");
    }

    private function buildPlugin(
        string $basePath,
        string $name,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): void {

        $pluginDir = $basePath . '/IdnoPlugins/' . $name;
        if (!is_dir($pluginDir)) {
            // Try ConsolePlugins
            $pluginDir = $basePath . '/ConsolePlugins/' . $name;
            if (!is_dir($pluginDir)) {
                $output->writeln("<error>Plugin directory not found: {$name}</error>");
                return;
            }
        }

        $domain = strtolower($name);
        $generator = new PotGenerator();
        $generator->extractFromDirectory($pluginDir, $pluginDir);

        $outputDir = $pluginDir . '/languages';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $potFile = $outputDir . '/' . $domain . '.pot';
        $content = $generator->generatePot($domain);
        file_put_contents($potFile, $content);

        $count = count($generator->getStrings());
        $relative = str_starts_with($pluginDir, $basePath)
            ? substr($pluginDir, strlen($basePath) + 1)
            : $pluginDir;
        $output->writeln("{$name}: extracted {$count} strings -> {$relative}/languages/{$domain}.pot");
    }

    private function buildTheme(
        string $basePath,
        string $name,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): void {

        $themeDir = $basePath . '/Themes/' . $name;
        if (!is_dir($themeDir)) {
            $output->writeln("<error>Theme directory not found: {$name}</error>");
            return;
        }

        $domain = strtolower($name);
        $generator = new PotGenerator();
        $generator->extractFromDirectory($themeDir, $themeDir);

        $outputDir = $themeDir . '/languages';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $potFile = $outputDir . '/' . $domain . '.pot';
        $content = $generator->generatePot($domain);
        file_put_contents($potFile, $content);

        $count = count($generator->getStrings());
        $output->writeln("{$name}: extracted {$count} strings -> Themes/{$name}/languages/{$domain}.pot");
    }

    private function buildAll(string $basePath, \Symfony\Component\Console\Output\OutputInterface $output): void
    {
        $output->writeln('Building all language files...');
        $output->writeln('');

        // Core
        $this->buildCore($basePath, $output);

        // IdnoPlugins
        $pluginDir = $basePath . '/IdnoPlugins';
        if (is_dir($pluginDir)) {
            foreach (new \DirectoryIterator($pluginDir) as $item) {
                if ($item->isDot() || !$item->isDir()) {
                    continue;
                }
                $this->buildPlugin($basePath, $item->getFilename(), $output);
            }
        }

        // ConsolePlugins
        $consoleDir = $basePath . '/ConsolePlugins';
        if (is_dir($consoleDir)) {
            foreach (new \DirectoryIterator($consoleDir) as $item) {
                if ($item->isDot() || !$item->isDir()) {
                    continue;
                }
                $this->buildPlugin($basePath, $item->getFilename(), $output);
            }
        }

        // Themes
        $themeDir = $basePath . '/Themes';
        if (is_dir($themeDir)) {
            foreach (new \DirectoryIterator($themeDir) as $item) {
                if ($item->isDot() || !$item->isDir()) {
                    continue;
                }
                $this->buildTheme($basePath, $item->getFilename(), $output);
            }
        }

        $output->writeln('');
        $output->writeln('Done.');
    }
}
