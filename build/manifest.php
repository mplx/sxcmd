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
$phar = __DIR__ . '/sxcmd-'.$argv[1].'.phar';

if (file_exists($phar)) {
    $sha1 = sha1_file($phar);
} else {
    echo "ERROR: target build not found!?" . PHP_EOL;
    exit(74); // EX_IOERR
}

try {
    $release = array(
        'name' => 'sxcmd.phar',
        'version' => $argv[1],
        'sha1' => $sha1,
        'url' => 'http://download.mplx.eu/download/sxcmd/release/?file=sxcmd-'.$argv[1].'.phar&forcedownload=1'
    );

    $data = file_get_contents($fmanifest);
    $data = json_decode($data);
    array_push($data, $release);
    $data = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($fmanifest, $data);
} catch (Exception $e) {
    echo "ERROR: failed building manifest..." . PHP_EOL;
    exit(74); // EX_IOERR
}

exit(0); // EX_OK
