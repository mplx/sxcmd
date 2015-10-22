<?php
/**
 * sxcmd
 * simple commandline interface to a skylable sx cluster
 *
 * @package     sxcmd
 * @author      Martin Pircher <mplx+coding@donotreply.at>
 * @copyright   Copyright (c) 2014-2015, Martin Pircher
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 **/

namespace mplx\sxcmd;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends Command
{
    const MANIFEST_FILE = 'http://mplx.github.io/sxcmd/manifest.json';

    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Updates sxcmd.phar to the latest version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentversion = $this->getApplication()->getVersion();

        $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $manager->update($currentversion, true);
    }
}