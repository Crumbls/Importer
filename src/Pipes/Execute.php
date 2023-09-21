<?php

namespace Crumbls\Importer\Pipes;

use Closure;
use Crumbls\Importer\Contracts\AbstractImport;
use Crumbls\Importer\Enums\ImportStatus;
use Crumbls\Importer\Exceptions\DriverNotSupported;
use Crumbls\Importer\Exceptions\MediaNotDefined;
use Crumbls\Importer\Exceptions\NoContentReturned;

/**
 * Determine a files mime type.
 */
class Execute
{
	public function handle(AbstractImport $model, Closure $next)
	{
		$status = strtoupper(class_basename(__CLASS__));
		$model->status = constant(ImportStatus::class.'::'.$status)->value;

		$media = $model->getMedia()->last();

		if (!$media) {
			throw new MediaNotDefined($media);
		}

		try {
			$importer = app('import')->driver($model->driver);
		} catch (\InvalidArgumentException $e) {
			throw new DriverNotSupported();
		}

		$content = $importer->media($media, (array)$model->settings);

		if (!$content) {
			throw new NoContentReturned();
		}

		$model->content = $content;
		$model->saveQuietly();

		return $next($model);
	}
}