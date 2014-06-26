<?php
require_once("twitter_feed.class.php");

$feeds = new TweetLoader();
$feeds->username = "google";
$feeds->consumerkey = "";
$feeds->consumersecret = "";
$feeds->accesstoken = "";
$feeds->accesstokensecrety = "";

print_r($feeds->loadFeed());
?>