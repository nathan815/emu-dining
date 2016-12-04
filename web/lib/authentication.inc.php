<?php

set_include_path(get_include_path() . PATH_SEPARATOR . "../../vendor/");

include_once "autoload.php";

$dev_key = "dev-key";

function login() {
	global $dev_key;
	
	$client = new Google_Client();
	$client->setDeveloperKey($dev_key);
	$client->setAuthConfig("../../conf/google_client_id.json");
	$client->setScopes("email");
	$auth_url = $client->createAuthUrl();

	header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}

function auth() {
	global $dev_key;
	$client = new Google_Client();

	$client->setDeveloperKey($dev_key);
	$client->setAuthConfig("../../conf/google_client_id.json");
	$client->setScopes("email");

	$client->authenticate($_GET['code']);
	$_SESSION['access_token'] = $client->getAccessToken();
}

function get_user_name() {
	global $dev_key;
	$client = new Google_Client();

	$client->setDeveloperKey($dev_key);
	$client->setAuthConfig("../../conf/google_client_id.json");
	$client->setScopes("email");

	if (isset($_SESSION['access_token'])) {
		$data = $client->verifyIdToken($_SESSION['access_token']['id_token']);
	}

	$service = new Google_Service_Plus($client);
	return $service->people->get($data['sub'])->displayName;
}

function get_user_name_from_id($id) {
	global $dev_key;
	$client = new Google_Client();
	$client->setDeveloperKey($dev_key);
	$client->setAuthConfig("../../conf/google_client_id.json");
//	$client->setRedirectUri("http://localhost:8080/?p=auth");
	$client->setScopes("email");

	$service = new Google_Service_Plus($client);
	return $service->people->get(strval($id))->displayName;
}

function get_user_id() {
	global $dev_key;
	$client = new Google_Client();

	$client->setDeveloperKey($dev_key);
	$client->setAuthConfig("../../conf/google_client_id.json");
	$client->setScopes('email');

	if (isset($_SESSION['access_token'])) {
		$data = $client->verifyIdToken($_SESSION['access_token']['id_token']);
		return intval($data['sub']);
	} else {
		return null;
	}
}