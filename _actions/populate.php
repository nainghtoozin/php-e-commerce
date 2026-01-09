<?php

include("../vendor/autoload.php");

use Faker\Factory as Faker;
use Libs\Database\CategoriesTable;
use Libs\Database\MySQL;
use Libs\Database\UsersTable;

$faker = Faker::create();
$table = new CategoriesTable(new MySQL());

if ($table) {
	echo "Database connection opened.\n";

	for ($i = 0; $i < 10; $i++) {
		$data = [
			'name' => $faker->name
		];

		$table->insert($data);
	}

	echo "Done populating users table.\n";
}
