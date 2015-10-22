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

$fmanifest = __DIR__ . '/../manifest.json';

$release = array(
    'name' => 'sxcmd.phar',
    'version' => $argv[1],
    'sha1' => sha1_file(__DIR__ . '/sxcmd.phar'),
    'url' => 'http://download.mplx.eu/sxcmd/release/sxcmd-'.$argv[1].'.phar'
);

$data = file_get_contents($fmanifest);
$data = json_decode($data);
array_push($data, $release);
$data = json_encode($data);
file_put_contents($fmanifest, $data);
