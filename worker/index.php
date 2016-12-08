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

$tip = $payload['tip'];
$id = hexdec(substr($tip, 0, 16));

$hash = new Flexihash();
$hash->addTargets($rts);
$shards = $hash->lookupList($tip, 2);

foreach ($shards as $host) {
    $conn = new Connection();
    $conn->setParams(['host' => $host, 'port' => 9306]);

    $q = SphinxQL::create($conn)
        ->insert()
        ->into('eno')
        ->value('id', $id)
        ->value('dummy', NULL)
        ->value('eno', $rawPayload);

    $result = $q->execute();

    var_dump($tip, $id, $host, $result);
}
