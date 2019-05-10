<?php

//TEST


$now = time( );
$then = gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $now + 365*86440);
header("Expires: $then");
$etag = md5($_GET['version']);
header("Etag: $etag");
header('Cache-Control: public, max-age=864000');

if (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
    header("HTTP/1.1 304 Not Modified");
    exit;
}

echo "4.2.0";
echo $etag;
