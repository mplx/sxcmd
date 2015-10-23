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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class VolumeListCommand extends SxCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('volume:list')
            ->setDescription('volume list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $volumes = $this->sx->getVolumeList();

        $volinfo=array();
        foreach ($volumes->volumeList as $vol => $meta) {
            $volinfo[] = array(
                $vol,
                $meta->replicaCount,
                $meta->maxRevisions,
                $meta->privs,
                $meta->owner,
                Util::prettyBytes($meta->sizeBytes),
                Util::prettyBytes($meta->usedSize)
            );
        }

        $table = new Table($output);
        $table->setStyle('default');
        $table
            ->setHeaders(array('Volume', 'Repl', 'Revs', 'Privs', 'Owner', 'Size', 'Used'))
            ->setRows($volinfo);
        $table->render();
    }
}
