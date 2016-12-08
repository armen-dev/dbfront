<?php

use Flexihash\Flexihash;
use Foolz\SphinxQL\Connection;
use Foolz\SphinxQL\SphinxQL;

require_once implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);

$rts = gethostbynamel(getenv('RT_INDEX_CLUSTER'));
$rawPayload = file_get_contents('php://input');
$payload = json_decode($rawPayload, TRUE);

if (!is_array($payload)) {
    header('HTTP/1.1 503 Service Unavailable');
    die('503 Service Unavailable');
}

// $tip = $payload['tip'];
// $id = hexdec(substr(hash('sha256', $tip), 0, 16));

$hash = new Flexihash();
$hash->addTargets($rts);

$partitions = [];
foreach ($rts as $host) {
    $partitions[$host] = [];
}

foreach ($payload as $eno) {
    $sid = $payload['sid'];
    $replicas = $hash->lookupList($sid, 2);
    $id = hexdec(substr($sid, 0, 15));

    foreach ($replicas as $replica) {
        $partitions[$replica][] = ['id' => $id, 'eno' => json_encode($eno)];
    }
}

foreach ($partitions as $host => $records) {
    try {
        $conn = new Connection();
        $conn->setParams(['host' => $host, 'port' => 9306]);

        $q = SphinxQL::create($conn);
        foreach ($records as $record) {
            $q->insert()->into('eno')->set($record)->enqueue();
        }
        $q->executeBatch();
    } catch (\Exception $e) {
        header('HTTP/1.1 503 Service Unavailable');
        die($e->getMessage());
    }
}
