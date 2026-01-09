<?php

use Libs\Database\CategoriesTable;
use Libs\Database\MySQL;

include("../../vendor/autoload.php");

$id = $_POST['id'];
$data = [
    'name' => $_POST['name'],
    'description' => $_POST['description'] ?? null,
    'image' => null,
];
if (!empty($_FILES['image']['name'])) {

    $imageTmp  = $_FILES['image']['tmp_name'];
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    // unique name (important)
    $imageName = uniqid('cat_') . '.' . $extension;

    $uploadPath = '../../public/uploads/categories/' . $imageName;

    move_uploaded_file($imageTmp, $uploadPath);

    $data['image'] = $imageName;
}
$data['image'] = $data['image'] ?? $_POST['existing_image'];
$table = new CategoriesTable(new MySQL());
if ($table->update($id, $data)) {
    header("Location: index.php?success=Category updated successfully");
    exit();
} else {
    header("Location: edit.php?id=$id&error=Failed to update category");
    exit();
}
