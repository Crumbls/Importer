<?php

return [
	'models' => [
		'importer' => \Crumbls\Importer\Models\Import::class
	],
	'driver_default' => 'csv',
	'drivers' => [
		'csv' => \Crumbls\Importer\Drivers\Csv::class
	],
	'csv' => [
		'filter_blank_lines' => true,
		'trim_columns' => true
	]
];