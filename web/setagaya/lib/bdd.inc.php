<?php
$url = parse_url ( getenv ( "CLEARDB_DATABASE_URL" ) );
$host = $url ["host"];
$user = $url ["user"];
$pass = $url ["pass"];
$db = substr ( $url ["path"], 1 );

$link = mysql_connect ( $host, $user, $pass );
if (! $link) {
	die ( 'Not connected : ' . mysql_error () );
}
$db_selected = mysql_select_db ( $db );
if (! $db_selected) {
	die ( 'Can\'t use $db : ' . mysql_error () );
}
mysql_set_charset ( 'utf8_bin' );
$socketIo = false;
?>