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
$sid = $payload['sid'];
$id = hexdec(substr($sid, 0, 16));

$hash = new Flexihash();
$hash->addTargets($rts);
$shards = $hash->lookupList($sid, 2);

foreach ($shards as $host) {
    $conn = new Connection();
    $conn->setParams(['host' => $host, 'port' => 9306]);

    try {
        $q = SphinxQL::create($conn)
            ->insert()
            ->into('eno')
            ->value('id', $id)
            ->value('eno', $rawPayload);

        $q->execute();
    } catch (\Exception $e) {
        header('HTTP/1.1 503 Service Unavailable');
        die($e->getMessage());
    }
}
