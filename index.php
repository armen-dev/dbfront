<?php

$server = getenv(strtoupper(str_replace('-', '_', getenv("SPHINX_SERVICE_NAME")))."_SERVICE_HOST");

var_dump($_ENV, $server);
