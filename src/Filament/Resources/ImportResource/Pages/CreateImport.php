<?php

namespace Crumbls\Importer\Filament\Resources\ImportResource\Pages;

use Crumbls\Importer\Filament\Resources\ImportResource;
use Crumbls\Importer\Jobs\ProcessImport;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateImport extends CreateRecord
{
    protected static string $resource = ImportResource::class;


	protected function handleRecordCreation(array $data): Model
	{
		$model = static::getModel()::create($data);
		ProcessImport::dispatch($model)->delay(now()->addSeconds(5));
		return $model;
	}
}
