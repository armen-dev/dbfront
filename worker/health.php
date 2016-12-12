<?php

use Foolz\SphinxQL\Connection;
use Foolz\SphinxQL\Helper;

require_once implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);

$result = false;
$alive = [];
$rts = gethostbynamel(getenv('RT_INDEX_CLUSTER'));

foreach ($rts as $host) {
    try {
        $conn = new Connection();
        $conn->setParams(['host' => $host, 'port' => 9306]);
        Helper::create($conn)->showStatus();
        $result = true;
    } catch (\Exception $e) {
        $result = false;
    }

    if ($result === true) {
        $alive[] = $host;
    }
}

if (count($alive) < 2) {
    header('HTTP/1.1 503 Service Unavailable');
    die(sprintf('Connection failed. (number of alive replicas: %d)', count($alive)));
}

echo 'Alive: '.implode(', ', $alive);
