<?php

require "sphinxapi.php";

try {
    $server = getenv(strtoupper(str_replace('-', '_', getenv("SPHINX_SERVICE_NAME")))."_SERVICE_HOST");

    $s = new SphinxClient();
    $s->setServer($server, 6712);

    $result = $s->status();
} catch (\Exception $e) {
    $result = false;
}

// Check connection
if ($result == false) {
    header("HTTP/1.1 503 Service Unavailable");
    die("Connection failed");
}

echo "OK";
