<?php
$url = parse_url ( getenv ( "CLEARDB_DATABASE_URL" ) );
$host = $url ["host"];
$user = $url ["user"];
$pass = $url ["pass"];
$db = substr ( $url ["path"], 1 );

$mysqli = new mysqli ( $host, $user, $pass, $db );
/*
 * This is the "official" OO way to do it,
 * BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
 */
if ($mysqli->connect_error) {
	die ( 'Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error );
}

/*
 * Use this instead of $connect_error if you need to ensure
 * compatibility with PHP versions prior to 5.2.9 and 5.3.0.
 */
if (mysqli_connect_error ()) {
	die ( 'Connect Error (' . mysqli_connect_errno () . ') ' . mysqli_connect_error () );
}

$res = $mysqli->query ( "SHOW TABLES LIKE users" );
if (mysqli_num_rows ( $res ) == 0) {
	$sqlSource = file_get_contents ( 'demo.sql' );
	mysqli_multi_query ( $sql, $sqlSource );
}

$socketIo = false;
?>