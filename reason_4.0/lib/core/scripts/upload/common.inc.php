<?php

/**
 * Common support code for the background upload scripts.
 *
 * @package reason
 * @subpackage scripts
 * @since Reason 4.0 beta 8
 * @author Eric Naeseth <enaeseth+reason@gmail.com>
 */

require_once 'reason_header.php';

// Prevent the error handler from dumping HTML error messages.
error_handler_config('script_mode', true);

reason_require_once('function_libraries/util.php');
reason_require_once('classes/entity_selector.php');
connectDB(REASON_DB);
reason_require_once('function_libraries/user_functions.php');
reason_require_once('function_libraries/upload.php');
reason_require_once('function_libraries/reason_session.php');

function response_code($code, $description) {
	$proto = (!empty($_SERVER['SERVER_PROTOCOL']))
		? $_SERVER['SERVER_PROTOCOL']
		: 'HTTP/1.0';
	header("$proto $code $description");
}

function final_response($code, $message) {
	static $code_descriptions = array(
		200 => "OK",
		202 => "Accepted",
		400 => "Bad Request",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Validation Failed", // XXX
		413 => "Request Entity Too Large",
		415 => "Unsupported Media Type",
		500 => "Internal Server Error",
		503 => "Service Unavailable"
	);
	
	if (is_array($message) || is_object($message)) {
		header('Content-Type: application/json');
		$message = json_encode($message);
	} else {
		header('Content-Type: text/plain');
	}
	
	response_code($code, $code_descriptions[$code]);
	echo trim($message)."\n";
	exit;
}

if (HTTPS_AVAILABLE && !on_secure_page()) {
	final_response(403, "This script must be accessed over a secure ".
		"connection.");
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	final_response(405, "This script must be accessed via a POST request.");
}

if (defined('MAINTENANCE_MODE_ON') && MAINTENANCE_MODE_ON) {
	final_response(503, "This site is currently undergoing maintenance. ".
		"Uploads cannot be accepted at this time.");
}

// When Flash is running as an NPAPI plugin under Windows, it does not send
// the correct cookies with HTTP requests, but instead sends whatever cookies
// are associated with its IE plugin version. SWFUpload instances are made to
// pass the session ID explicitly to work around this.
$reason_session =& get_reason_session();
if (!empty($_REQUEST['reason_sid'])) {
	$reason_session->start($_REQUEST['reason_sid']);
} else {
	$reason_session->start();
}

$upload_sid = @$_REQUEST['upload_sid'];
$session = _get_async_upload_session($upload_sid);
if (!$session) {
	if (empty($_REQUEST['upload_sid'])) {
		final_response(400, "Upload session (upload_sid) not provided.");
	} else {
		final_response(400, "No upload session with ID ".
			$upload_sid);
	}
}

// Permission check.
if (!can_upload($session)) {
	final_response(403, "Permission denied.");
}

function can_upload($session) {
	if ($session['authenticator']) {
		$auth = $session['authenticator'];
		$reason_session =& get_reason_session();
		$username = $reason_session->get("username");
		
		if ($auth['file'])
			require_once $auth['file'];
		
		$args = array_merge(array($username), $auth['arguments']);
		if (!call_user_func_array($auth['callback'], $args))
			return false;
	}
	
	return true;
}
