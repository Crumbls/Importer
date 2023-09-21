<?php

namespace Crumbls\Importer\Filament\Resources\ImportResource\Pages;

use Crumbls\Importer\Filament\Resources\ImportResource;
use Crumbls\Importer\Jobs\ProcessImport;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditImport extends EditRecord
{
    protected static string $resource = ImportResource::class;

    protected function getHeaderActions(): array
    {
//	    dd(get_called_class(),get_class_methods(get_called_class()));

	    return [
            Actions\DeleteAction::make(),
        ];
    }

	/**
	 * @param  array<string, mixed>  $data
	 */
	protected function handleRecordUpdate(Model $record, array $data): Model
	{
		$record->update($data);

		\Crumbls\Importer\Jobs\ProcessImport::dispatch($record);

		return $record;
	}
}
