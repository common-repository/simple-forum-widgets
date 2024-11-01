<?php

$SFRequest = simple_forum_widgets_get_value('SFRequest', $_GET);
if ($SFRequest == 'connect') {
	$_GET['timestamp'] = $_GET['_'];
	require_once dirname(__FILE__).'/simple-forum-widgets-functions.php';
	$user = simple_forum_widgets_get_user();
	$ID = get_option('sf_client_id');
	$secret = get_option('sf_secret');
	$data = WriteSFConnect($user, $_GET, $ID, $secret, true);
	header("Content-Type: application/json");
	echo $data;
	exit();

} elseif ($SFRequest == 'logout') {
	simple_forum_widgets_logout_user($_GET['return_url']);
	exit();
} elseif ($SFRequest == 'generate-secret') {
	// echo secret
	$secret = sha1(time());
	if (isset($_GET['length'])) {
		$secret = substr($secret, 0, $_GET['length']);
	}
	echo $secret;
	exit();
}
