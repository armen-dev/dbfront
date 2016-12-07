<?php

require_once implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'lib', 'sphinxapi.php']);

try {
    $server = getenv('RT_INDEX_CLUSTER');

    $s = new SphinxClient();
    $s->setServer($server, 9312);

    $result = $s->status();
} catch (\Exception $e) {
    $result = false;
}

// Check connection
if ($result == false) {
    header('HTTP/1.1 503 Service Unavailable');
    die('Connection failed');
}

echo 'OK';
