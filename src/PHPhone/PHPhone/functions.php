<?php

use PHPhone\PHPhone\Utils\Logger;

/**
 * User: Mike Knooihuisen
 * @version 11/13/18
 */

function logger($level = null, $message = null) {
	return Logger::getInstance($level, $message);
}

function env($varname, $default = false) {
	$varname = getenv($varname);
	return ($varname === false)? $default : $varname;
}