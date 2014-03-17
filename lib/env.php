<?php

require "lib/err.php";

date_default_timezone_set('UTC');
define('JSON_FLAGS', JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

// is_dry_run returns true if --dry-run is specified.
function is_dry_run() {
	global $argv;
	foreach($argv as $arg) {
		if($arg == '--dry-run') {
			return true;
		}
	}
	return false;
}

// fprintln writes a line of space-separated args to fd.
// non-strings are first json encoded.
function fprintln($fd , $args /*...*/) {
	$n = func_num_args();
	for($i = 1; $i < $n; $i++) {
		if($i > 1) {
			fwrite($fd, " ");
		}
		$arg = func_get_arg($i);
		if(!is_string($arg)) {
			$arg = json_encode($arg, JSON_UNESCAPED_SLASHES);
		}
		fwrite($fd, $arg);
	}
	fwrite($fd, "\n");
}

// println writes a line of space-separated args to stdout.
// non-strings are first json encoded.
function println($args /*...*/) {
	$args = func_get_args();
	array_unshift($args, STDOUT);
	call_user_func_array('fprintln', $args);
}

// fail writes a line of space-separated args to stderr then exits 1.
// non-strings are first json encoded.
function fail($args /*...*/) {
	$args = func_get_args();
	array_unshift($args, STDERR);
	call_user_func_array('fprintln', $args);
	exit(1);
}

// failf writes a line to stderr according to format, then exits 1.
function failf($format, $args /*...*/) {
	$args = array_slice(func_get_args(), 1);
	array_unshift($args, STDERR, "$format\n");
	call_user_func_array('fprintf', $args);
	exit(1);
}

// json_input returns json read from stdin. errors are fatal.
function json_input() {
	$data = rtrim(stream_get_contents(STDIN));
	if(!$data) {
		fail("JSON decode error: empty input");
	}
	$data = json_decode($data, true);
	if(json_last_error() !== JSON_ERROR_NONE) {
		fail("JSON decode error:", json_last_error_msg());
	}
	return $data;
}

// json_output encodes and writes data as json to stdout. errors are fatal.
function json_output($data) {
	$out = json_encode($data, JSON_FLAGS);
	if($out === FALSE) {
		fail("JSON encode error:", json_last_error_msg());
	}
	println($out);
}

// timestamp_decode decodes an rfc3339/iso8601 string and
// returns a unix timestamp on success, or false on error.
function timestamp_decode($rfc3339) {
	return strtotime($rfc3339);
}

// timestamp_encode accepts a timestamp and returns an rfc3339 string.
// If timestamp isn't a number, a fatal error occurs.
function timestamp_encode($timestamp) {
	is_numeric($timestamp) or fail("timestamp_encode called with non-number:", $timestamp);
	$date = date('c', $timestamp);
	if(substr($date, -5) === '00:00') {
		$date = substr($date, 0, -6) . 'Z';
	}
	return $date;
}

function bound() {
	println('---');
}
