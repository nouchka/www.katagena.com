<?php

//TEST

$now = time( );
$then = gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $now + 365*86440);
$etag = md5($_GET['version']);
header("Expires: $then");
header("Etag: $etag");

echo "4.2.0";
