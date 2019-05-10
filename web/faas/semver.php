<?php

//TEST

$etag = md5($_GET['version']);
header("Etag: $etag");
header('Cache-Control: public, max-age=864000');

echo "4.2.0";
echo $etag;
