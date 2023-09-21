<?php

namespace Crumbls\Importer\Pipes;

use Closure;
use Crumbls\Importer\Contracts\AbstractImport;
use Crumbls\Importer\Enums\ImportStatus;
use Crumbls\Importer\Exceptions\DriverNotSupported;
use Crumbls\Importer\Exceptions\MediaNotDefined;

/**
 * Determine a files mime type.
 */
class Driver
{
	public function handle(AbstractImport $model, Closure $next)
	{
		$status = strtoupper(class_basename(__CLASS__));
		$model->status = constant(ImportStatus::class.'::'.$status)->value;

		if (!$model->driver || !is_string($model->driver)) {
			$media = $model->getMedia()->last();
			if (!$media) {
				throw new MediaNotDefined($media);
			}

			$importer = app('import');
			$drivers = array_filter(array_keys($importer->getDrivers()), 'is_string');

			$driver = false;

			foreach($drivers as $key) {
				try {
					if ($importer->driver($key)->supports($model, $media)) {
						$driver = $key;
						break;
					}
				} catch (\Throwable $e) {
					echo 'tf';
					exit;
				}
			}

			if (!$driver) {
				throw new DriverNotSupported();
			}

			$model->update(['driver' => $driver]);
		}
		return $next($model);
	}
}