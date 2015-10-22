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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class VolumeLocateCommand extends SxCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('volume:locate');
        $this->setDescription('volume location information');

        $this->addArgument('volume', InputArgument::REQUIRED);
        $this->addOption('reverse-lookup', 'r', InputOption::VALUE_NONE, 'reverse dns lookup');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $volume = $input->getArgument('volume');
        $rdns = $input->getOption('reverse-lookup');

        $locate = $this->sx->locateVolume($volume);

        $nodes = array();
        foreach ($locate->nodeList as $node => $ip) {
            $nodes[] = array(
                $node,
                $ip,
                Util::rdns($ip, $rdns)
            );
        }

        $table = new Table($output);
        $table->setStyle('default');
        $table
            ->setHeaders(array('Node', 'IP', 'DNS'))
            ->setRows($nodes);
        $table->render();
    }
}
