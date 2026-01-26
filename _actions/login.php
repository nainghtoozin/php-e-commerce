<?php
include(__DIR__ . '/../vendor/autoload.php');

use Libs\Database\MySQL;
use Libs\Database\UsersTable;
use Helpers\HTTP;

session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    HTTP::redirect('/login.php', 'incorrect=1');
    exit;
}

$table = new UsersTable(new MySQL());
$user = $table->findByEmailAndPassword($email, $password);

if (!$user) {
    HTTP::redirect('/login.php', 'incorrect=1');
    exit;
}

// Check if user is suspended
if ($user->suspended == 1) {
    HTTP::redirect('/login.php', 'suspended=1');
    exit;
}

// Set session
$_SESSION['user'] = $user;

// Redirect to dashboard
HTTP::redirect('/_actions/dashboard.php');
exit;
