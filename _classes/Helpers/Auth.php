<?php

namespace Helpers;

class Auth
{
	static $loginUrl = '/login.php';

	static function check()
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		
		if (isset($_SESSION['user'])) {
			return $_SESSION['user'];
		} else {
			HTTP::redirect(static::$loginUrl);
		}
	}
}
