<?php
include(__DIR__ . '/../vendor/autoload.php');

use Helpers\HTTP;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login
HTTP::redirect('/login.php');
