<?php

namespace Crumbls\Importer\Pipes;

use Closure;
use Crumbls\Importer\Contracts\AbstractDriver;
use Crumbls\Importer\Contracts\AbstractImport;
use Crumbls\Importer\Enums\ImportStatus;
use Crumbls\Importer\Exceptions\MediaNotDefined;
use Crumbls\Importer\Services\ImportManager;

/**
 * Determine a files encoding.
 * TODO: Support external disks eventually
 */
class FileEncoding
{
	public function handle(AbstractImport $model, Closure $next)
	{
		$status = strtoupper(class_basename(__CLASS__));
		$model->status = constant(ImportStatus::class.'::'.$status)->value;

		$changed = false;
		$temp = $model->settings;

		if (!is_array($temp)) {
			$changed = true;
			$temp = [];
		}

		if (!array_key_exists('input', $temp) || !is_array($temp['input'])) {
			$changed = true;
			$temp['input'] = [];
		}

		if (!array_key_exists('encoding', $temp['input']) || !is_string($temp['input']['encoding']) || !$temp['input']['encoding']) {
			$changed = true;

			$media = $model->getMedia()->last();
			if (!$media) {
				throw new MediaNotDefined($media);
			}

			$temp['input']['encoding'] = ImportManager::detectFileEncoding($media->getPath());
		}
		if ($changed) {
			$model->settings = $temp;
			$model->saveQuietly();
		}

		return $next($model);
	}
}