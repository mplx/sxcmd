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

use mplx\skylablesx\Sx;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

use \Exception;

abstract class SxCommand extends Command
{
    protected $config;
    protected $sx;
    protected $name;
    protected $configpath;

    public function __construct()
    {
        if (isset($_SERVER['HOME'])) {
            $this->configpath = $_SERVER['HOME'] . '/.sxcmd/';
        }elseif (isset($_SERVER['LOCALAPPDATA'])) {
            $this->configpath = $_SERVER['LOCALAPPDATA'] . '\mplx\sxcmd';
        } else {
            $this->configpath = './';
        }


        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('cluster', InputArgument::REQUIRED);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $yaml = new Parser();
        $this->name = $input->getArgument('cluster');

        try {
            $cfg = @file_get_contents($this->configpath . $this->name . '.yml');
            if ($cfg === false) {
                throw new Exception('Error reading cluster configuration');
            }
            $this->config = $yaml->parse($cfg);
        } catch (ParseException $e) {
            $output->writeln('Unable to parse cluster configuration');
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }

        try {
            $this->sx = new Sx();
            $this->sx->setAuth($this->config['authkey']);
            $this->sx->setEndpoint($this->config['cluster']);
            $this->sx->setPort($this->config['port']);
            if ($this->config['ssl'] == true) {
                if (isset($this->config['sslverify']) && $this->config['sslverify'] == true) {
                    $this->sx->setSSL(true, true);
                } else {
                    $this->sx->setSSL(true, false);
                }
            } else {
                $this->sx->setSSL(false);
            }
        } catch (Exception $e) {

        }
    }
}
