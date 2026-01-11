<?php

use Libs\Database\CategoriesTable;
use Libs\Database\MySQL;
use Libs\Database\ProductsTable;

include("../../vendor/autoload.php");

$id = $_GET['id'];
$table = new ProductsTable(new MySQL());
if ($table->delete($id)) {
    header("Location: index.php?success=Product deleted successfully");
    exit();
} else {
    header("Location: index.php?error=Failed to delete product");
    exit();
}
