<?php

namespace Crumbls\Importer;

use App\Models\Company;
use App\Models\User;
use Crumbls\Importer\Facades\Import;
use Crumbls\Importer\Filament\Resources\FormResource;
use Crumbls\Importer\Models\Position;
use Crumbls\Importer\Observers\PositionObserver;
use Crumbls\Importer\Services\ImportManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Support\Facades\Gate;

class ImporterServiceProvider extends ServiceProvider
{

	private static array $models = [];

	/**
	 * Get the model we use for the importer.
	 * @return string
	 */
	public static function getModelImporter() : string {
		if (!array_key_exists('importer', static::$models) || !static::$models['importer']) {
			static::$models['importer'] = \Config::get('importer.models.importer', \Crumbls\Importer\Models\Import::class);
		}
		return static::$models['importer'];
	}


	public function register() : void {
		$this->mergeConfigFrom(
			__DIR__.'/../config/importer.php', 'importer'
		);
		$this->registerFacade();
	}

	public function boot() : void {
		$this->loadViewsFrom(__DIR__.'/Views', 'importer');

		$this->publishes([
			__DIR__.'/../config/importer.php' => config_path('importer.php')
		]);

		$this->loadMigrationsFrom(__DIR__ .'/../database/migrations');
	}

	/**
	 * Bring our facade online.
	 */
	private function registerFacade() : void {
/*
		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		$loader->alias('Import', ImportManager::class);
*/
		$this->app->bind('import', function($app) {
			$manager = new ImportManager($app);
			return $manager;
		});
	}
}
