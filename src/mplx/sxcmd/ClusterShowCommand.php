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

use mplx\sxcmd\Util;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

use \Exception;

class ClusterShowCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cluster:show')
            ->setDescription('show cluster configuration')
            ->addArgument('cluster', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $home = Util::getSxCmdDir();
        $cluster = $input->getArgument('cluster') . '.yml';

        if (!file_exists($home)) {
            throw new Exception('Cannot find sxcmd configuration directory: ' . $home);
        } elseif (!file_exists($home . $cluster)) {
            throw new Exception('Cannot find cluster configuration file: ' . $cluster);
        }


        $cfg = file_get_contents($home . $cluster);
        if ($cfg === false) {
            throw new Exception('Error reading cluster configuration');
        }

        try {
            $yaml = new Parser();
            $cfg = $yaml->parse($cfg);
        } catch (ParseException $e) {
            throw new Exception('Unable to parse cluster configuration: ' . $cluster);
        }

        $table = new Table($output);
        $table->setStyle('default');
        $table->setRows(array(
            array('host', $cfg['cluster']),
            array('port', $cfg['port']),
            array('key', $cfg['authkey']),
            array('ssl', $cfg['ssl'] ? 'yes' : 'no'),
            array('sslverify', $cfg['sslverify'] ? 'yes' : 'no'),
        ));
        $table->render();
    }
}
