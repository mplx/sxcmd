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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

use \Exception;

class ClusterModifyCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cluster:modify')
            ->setDescription('add/modify a cluster configuration')
            ->addArgument('cluster', InputArgument::REQUIRED)
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'cluster host', null)
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'TCP port', null)
            ->addOption('key', null, InputOption::VALUE_REQUIRED, 'Authorization key', null)
            ->addOption('ssl', null, InputOption::VALUE_REQUIRED, 'SSL enabled', null)
            ->addOption('sslverify', null, InputOption::VALUE_REQUIRED, 'Verify SSL certificate', null)
            ->addOption('template', null, InputOption::VALUE_REQUIRED, 'cluster configuration template', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $home = Util::getSxCmdDir();
        if (!file_exists($home)) {
            throw new Exception('Cannot find sxcmd configuration directory: ' . $home);
        }

        $cluster = $input->getArgument('cluster') . '.yml';
        if ($input->getOption('template')) {
            $source = $home . $input->getOption('template') . '.yml';
            $target = $home . $cluster;
        } else {
            $source = $home . $cluster;
            $target = $home . $cluster;
        }

        if (file_exists($source)) {
            $yaml = new Parser();
            $cfg = @file_get_contents($source);
            if ($cfg === false) {
                throw new Exception('Error reading cluster configuration');
            }
            $config = $yaml->parse($cfg);
            $output->writeln('<info>Modifying cluster configuration ' . $cluster . '</info>');
        } else {
            $output->writeln('<info>Creating new cluster configuration ' . $cluster . '</info>');
            $config = array();
        }

        if ($input->getOption('host')) {
            $config['cluster'] = $input->getOption('host');
        }
        if ($input->getOption('port')) {
            $config['port'] = (integer)$input->getOption('port');
        }
        if ($input->getOption('key')) {
            $config['authkey'] = $input->getOption('key');
        }
        if ($input->getOption('ssl')) {
            $config['ssl'] = $input->getOption('ssl') == 'yes' ? true : false;
        }
        if ($input->getOption('sslverify')) {
            $config['sslverify'] = $input->getOption('sslverify') == 'yes' ? true : false;
        }

        if (isset($config['cluster']) && isset($config['port']) && isset($config['authkey']) &&
            isset($config['ssl']) && isset($config['sslverify'])) {
            $dumper = new Dumper();
            $yaml = $dumper->dump($config, 1);
            file_put_contents($target, $yaml);
        } else {
            dump($config);
            $output->writeln('<error>Cluster configuration incomplete. Aborted</error>');
        }
    }
}
