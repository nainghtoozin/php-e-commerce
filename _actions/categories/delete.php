<?php

use Libs\Database\CategoriesTable;
use Libs\Database\MySQL;
use Helpers\Auth;

include("../../vendor/autoload.php");

$auth = Auth::check();

$id = $_GET['id'];
$table = new CategoriesTable(new MySQL());
if ($table->delete($id)) {
    header("Location: index.php?success=Category deleted successfully");
    exit();
} else {
    header("Location: index.php?error=Failed to delete category");
    exit();
}
