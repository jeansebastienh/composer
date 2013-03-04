<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Command;

use Composer\Installer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jean-Sébastien Hedde <jeanseb@php.net>
 */
class GenerateAutoloadFiles extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate-autoload-files')
            ->setDescription('Generates autoloads files without downloading packages.')
            ->setDefinition(array(
                new InputOption('dev', null, InputOption::VALUE_NONE, 'Enables installation of require-dev packages.'),
                new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Disables installation of require-dev packages (enabled by default, only present for sanity).'),
                new InputOption('no-custom-installers', null, InputOption::VALUE_NONE, 'Disables all custom installers.'),
                new InputOption('no-progress', null, InputOption::VALUE_NONE, 'Do not output download progress.'),
                new InputOption('verbose', 'v', InputOption::VALUE_NONE, 'Shows more details including new commits pulled in when updating packages.'),
                new InputOption('optimize-autoloader', 'o', InputOption::VALUE_NONE, 'Optimize autoloader during autoloader dump')
            ))
            ->setHelp(<<<EOT
The <info>generate-autoload-files</info> command reads the composer.lock file from
the current directory, processes it, and generate autoload files based on ibraries
and dependencies outlined in that file. If the file does not exist it will look 
for composer.json and do the same.

<info>php composer.phar install</info>

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composer = $this->getComposer();
        $composer->getDownloadManager()->setOutputProgress(!$input->getOption('no-progress'));
        $io = $this->getIO();
        $install = Installer::create($io, $composer);

        $install
            ->setDryRun(true)
            ->setVerbose($input->getOption('verbose'))
            ->setDevMode($input->getOption('dev'))
            ->setOptimizeAutoloader($input->getOption('optimize-autoloader'))
        ;

        if ($input->getOption('no-custom-installers')) {
            $install->disableCustomInstallers();
        }

        return $install->run() ? 0 : 1;
    }
}
