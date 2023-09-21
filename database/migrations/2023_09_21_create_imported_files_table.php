<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	protected static ?string $tableName;

	/**
	 * Get our table name.
	 * @return string
	 */
	public static function getTableName() : string {
		if (!isset(static::$tableName)) {
			$classModel = \Config::get('importer.models.importer', \Crumbls\Importer\Models\Import::class);
			static::$tableName = with (new $classModel)->getTable();
		}
		return static::$tableName;
	}


	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		$tableName = static::getTableName();
		Schema::create($tableName, function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('tenant_id')->nullable()->default(null);
			$table->string('status')->default(\Crumbls\Importer\Enums\ImportStatus::getDefault());
			$table->string('driver', 256)->default(null)->nullable();
			$table->json('settings')->default(null)->nullable();
			$table->json('content')->default(null)->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists(static::getTableName());
	}
};
