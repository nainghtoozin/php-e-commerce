<?php
include("../../vendor/autoload.php");

use Libs\Database\MySQL;
use Libs\Database\ProductsTable;
use Helpers\Auth;

$auth = Auth::check();

function slugify($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.php');
    exit;
}

$data = [
    'name'              => $_POST['name'],
    'slug'              => slugify($_POST['name']),
    'sku'               => $_POST['sku'],
    'price'             => $_POST['price'],
    'category_id'       => $_POST['category_id'],
    'short_description' => $_POST['short_description'],
    'description'       => $_POST['description'],
    'quantity'          => $_POST['quantity'],   // 👈 inventory
    'status'            => $_POST['status'],
];


// 2️⃣ call model
$table = new ProductsTable(new MySQL());
$product = $table;

$result = $product->store($data, $_FILES);

if ($result) {
    header('Location: index.php?success=Product+created+successfully');
} else {
    header('Location: create.php?error=Product+creation+failed');
}
exit;
