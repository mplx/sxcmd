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

use mplx\skylablesx\SxException;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use \Exception;

class UserListCommand extends SxCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('user:list')
            ->setDescription('user list (requires admin permissions)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $users = $this->sx->getUserList();
        } catch (SxException $e) {
            throw new Exception('Cannot fetch user list: ' . $e->getMessage());
        }

        $userlist = array();
        foreach ($users as $user => $properties) {
            $userlist[] = array(
                $user,
                $properties->admin ? "yes" : "no"
            );
        }

        $table = new Table($output);
        $table->setStyle('default');
        $table
            ->setHeaders(array('User', 'Admin'))
            ->setRows($userlist);
        $table->render();
    }
}
