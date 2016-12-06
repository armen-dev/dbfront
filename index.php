<?php

$rt = gethostbyname(getenv('RT_INDEX_CLUSTER'));
$dist = gethostbyname(getenv('DIST_INDEX_CLUSTER'));

var_dump($_ENV, $rt, $dist);
