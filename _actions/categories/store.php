<?php

use Libs\Database\CategoriesTable;
use Libs\Database\MySQL;

include("../../vendor/autoload.php");

$data = [
    'name' => $_POST['name'],
    'description' => $_POST['description'] ?? null,
];

$imageName = null;

if (!empty($_FILES['image']['name'])) {

    $imageTmp  = $_FILES['image']['tmp_name'];
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    // unique name (important)
    $imageName = uniqid('cat_') . '.' . $extension;

    $uploadPath = '../../public/uploads/categories/' . $imageName;

    move_uploaded_file($imageTmp, $uploadPath);
}

$data['image'] = $imageName;
$table = new CategoriesTable(new MySQL());

if ($table->insert($data)) {
    header("Location: index.php?success=Category created successfully");
    exit();
} else {
    header("Location: create.php?error=Failed to create category");
    exit();
}
