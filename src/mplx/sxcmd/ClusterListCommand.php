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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

use \Exception;

class ClusterListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cluster:list')
            ->setDescription('list all configured cluster');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $home = Util::getSxCmdDir();

        if (!file_exists($home)) {
            throw new Exception('Cannot fine sxcmd configuration directory: ' . $home);
        }

        $cluster = array();

        $finder = new Finder();
        $finder->name('*.yml')->in($home);
        foreach ($finder as $file) {
            $yaml = new Parser();
            $cfg = @file_get_contents($file->getRealpath());
            if ($cfg === false) {
                throw new Exception('Error reading cluster configuration');
            }
            $cfg = $yaml->parse($cfg);

            $cluster[] = array(
                'config' => $file->getRelativePathname(),
                'cluster' => $cfg['cluster'] . ':' . $cfg['port'],
                'ssl' => $cfg['ssl'] ? 'yes' : 'no',
                'key' => Util::shortenKey($cfg['authkey'])
            );
        }

        $table = new Table($output);
        $table->setStyle('default');
        $table
            ->setHeaders(array('File', 'Cluster', 'SSL', 'Key'))
            ->setRows($cluster);
        $table->render();
    }
}
