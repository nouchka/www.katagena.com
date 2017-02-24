<?php
include '../../vendor/autoload.php';

use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Item;

$from = 'en';
if (isset ( $_GET ['from'] )) {
	$from = preg_replace ( '/[^a-z]/', '', $_GET ['from'] );
}
$learning = 'ja';
if (isset ( $_GET ['learning'] )) {
	$learning = preg_replace ( '/[^a-z]/', '', $_GET ['learning'] );
}
$url = 'https://incubator.duolingo.com/api/1/courses/show?learning_language_id=' . $learning . '&from_language_id=' . $from . '&ui_language_abbrev=en';
$response = Requests::get ( $url );
if ($response->status_code == "200") {
	$result = $response->body;
	$json = json_decode ( $result );
	
	$urlIncubator = 'https://incubator.duolingo.com/courses/' . $learning . '/' . $from . '/status';
	$feed = new Feed ();
	$channel = new Channel ();
	$channel->title ( 'Duolingo learning ' . $learning . ' from ' . $from );
	$channel->description ( 'Estimated date for ' . $learning );
	$channel->url ( $urlIncubator );
	$channel->language ( 'en-US' );
	$channel->pubDate ( strtotime ( 'today midnight' ) )->lastBuildDate ( strtotime ( 'today midnight' ) )->ttl ( 1440 );
	$channel->appendTo ( $feed );
	
	if (date ( 'Ymd' ) == date ( 'Ymd', strtotime ( $json->ecd_edited_by->datetime ) )) {
		// Blog item
		$item = new Item ();
		$item->title ( 'Update of duolingo beta ' . $learning );
		$item->description ( $json->estimated_completion_date );
		$item->contentEncoded ( $json->estimated_completion_date );
		$item->url ( $urlIncubator );
		$item->author ( 'Duolingo updates' );
		$item->pubDate ( strtotime ( 'today midnight' ) );
		$item->guid ( $urlIncubator . '?date=' . date ( 'Ymd' ), true );
		$item->preferCdata ( true );
		$item->appendTo ( $channel );
	}
	
	echo $feed->render ();
}
