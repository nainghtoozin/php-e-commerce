<?php

include("../vendor/autoload.php");

use Faker\Factory as Faker;
use Libs\Database\MySQL;
use Libs\Database\ProductsTable;
use Libs\Database\CategoriesTable;

$faker = Faker::create();
$db = new MySQL();

$productTable  = new ProductsTable($db);
$categoryTable = new CategoriesTable($db);

// get all categories
$categories = $categoryTable->getAll();

if (!$categories) {
	die("❌ No categories found. Please seed categories first.\n");
}

echo "✅ Database connection opened.\n";

for ($i = 0; $i < 30; $i++) {

	// random category
	$category = $faker->randomElement($categories);

	$name = $faker->words(3, true);

	$data = [
		'name'              => ucfirst($name),
		'slug'              => strtolower(str_replace(' ', '-', $name)),
		'sku'               => strtoupper($faker->bothify('SKU-###??')),
		'price'             => $faker->numberBetween(10, 500),
		'category_id'       => $category->id,
		'short_description' => $faker->sentence(8),
		'description'       => $faker->paragraph(3),
		'quantity'          => $faker->numberBetween(0, 100),
		'status'            => $faker->randomElement([0, 1]),
	];

	// 👇 reuse your existing store logic
	$productTable->store($data, [
		'images' => [
			'name' => [
				'product-main.jpg',
				'product-1.jpg',
				'product-2.jpg'
			],
			'tmp_name' => [
				null,
				null,
				null
			]
		]
	]);

	echo "✔ Inserted product: {$data['name']}\n";
}

echo "🎉 Done populating products table.\n";
