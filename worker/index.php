<?php

use Flexihash\Flexihash;
use Foolz\SphinxQL\Connection;
use Foolz\SphinxQL\SphinxQL;

require_once implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);

$rts = gethostbynamel(getenv('RT_INDEX_CLUSTER'));
$rawPayload = file_get_contents('php://input');
$payload = json_decode($rawPayload, TRUE);
$tip = $payload['tip'];

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

    var_dump($host, $result);
}
