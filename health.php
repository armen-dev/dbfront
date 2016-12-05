<?php

$server = getenv(strtoupper(getenv("SPHINX_SERVICE_NAME"))."_SERVICE_HOST");

$s = new SphinxClient();
$s->setServer($server, 6712);

$result = $s->status();

// Check connection
if ($result == FALSE) {
    header("HTTP/1.1 503 Service Unavailable");
    die("Connection failed");
}
echo "OK";
