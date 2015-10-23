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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use \Exception;

class FileUploadCommand extends SxCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('file:upload')
            ->setDescription('file upload')
            ->addArgument('source', InputArgument::REQUIRED)
            ->addArgument('target', InputArgument::REQUIRED)
            ->addOption('time', 't', InputOption::VALUE_NONE, 'time');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $target = explode(':', $input->getArgument('target'), 2);

        if (!file_exists($source)) {
            throw new Exception('Source file does not exist');
        }

        $output->writeln(
            'Uploading file <info>' . $source . '</info> ' .
            'to volume <info>' . $target[0] . ':' . $target[1] . '</info>'
        );

        $start = microtime(true);
        $result = $this->sx->uploadFile($target[0], $target[1], $source);
        while ($result->requestStatus == 'PENDING') {
            $output->writeln('<comment>Upload pending...</comment>');
            sleep(1);
            $result = $this->sx->getJobStatus($result->requestId, 1, 2, 1);
        }
        $end = microtime(true);

        if ($result->requestStatus == 'OK') {
            if ($input->getOption('time')) {
                $output->writeln(sprintf('<info>Upload sucessfull in %0.2f secs</info>', $end - $start));
            } else {
                $output->writeln('<info>Upload sucessfull</info>');
            }
        } elseif ($result->requestStatus == 'ERROR') {
            $output->writeln('<error>Error uploading file:</error> ' . $result->requestMessage);
        }
    }
}
