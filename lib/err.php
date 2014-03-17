<?php

error_reporting(E_ALL);
set_error_handler(function($errno, $msg, $file, $line, $ctx) {
	switch($errno) {
	case E_WARNING: case E_CORE_WARNING: case E_COMPILE_WARNING: case E_USER_WARNING:
		$type = "warning";
		break;
	case E_NOTICE: case E_USER_NOTICE: case E_STRICT:
		$type = "notice";
		break;
	default:
		$type = "error";
	}
	$pwd = realpath('.');
	if($pwd === substr($file, 0, strlen($pwd))) {
		$file = ltrim(substr($file, strlen($pwd)), '/');
	}
	quitf("%s:%d: %s: %s", $file, $line, $type, $msg);
});
