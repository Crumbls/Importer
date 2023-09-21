<?php

namespace Crumbls\Importer\Filament\Resources;

use Crumbls\Importer\Contracts\AbstractImport;
use Crumbls\Importer\Filament\Resources\ImportResource\Pages;
use Crumbls\Importer\Filament\Resources\ImportResource\RelationManagers;
use Crumbls\Importer\ImporterServiceProvider;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class ImportResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

	public static function getModel() : string {
		return ImporterServiceProvider::getModelImporter();
	}

    public static function form(Form $form): Form
    {
//		dd(get_called_class(),get_class_methods(get_called_class()));
		/*
		dd(get_class_methods(SpatieMediaLibraryFileUpload::make('file')
			->required()));
		*/
        return $form
            ->schema([
				Forms\Components\TextInput::make('mime_type')
					->hidden(true)
					->dehydrateStateUsing(function($state) {
						dd($state);
					})
					->afterStateUpdated(function (Set $set, $state) {
						dd($state);
					}),
	            SpatieMediaLibraryFileUpload::make('file')
	                ->required()
		            ->columnSpan('full')
		            ->reactive()
		            ->afterStateHydrated(function(\Filament\Forms\Components\SpatieMediaLibraryFileUpload $component, string|array|null $state, AbstractImport $record = null, Set $set) {
						if ($record && $record->getKey() && $media = $record->getMedia()->last()) {
							try {
								$set('mime_type', $media->mime_type);
							} catch (Throwable $e) {
								$set('mime_type', '');
							}
						} else {
							$set('mime_type', '');
						}
		            })
		            ->dehydrateStateUsing(function($state) {
						dd($state);
		            })
		            ->afterStateUpdated(function (Set $set, $state) {
						try {
							$set('mime_type', $state->getMimeType());
						} catch (Throwable $e) {
							dd($e);
							$set('mime_type', '');
						}
		            }),

	            /**
	             * CSV Settings.
	             */
	            Section::make('CSV Settings')
		            ->description('These settings are specific to CSV imports.')
		            ->schema([
			            Forms\Components\TextInput::make('settings.input.enclosure')
				            ->label('Enclosure')
				            ->afterStateHydrated(function(string|array|null $state, AbstractImport $record = null, Set $set, Get $get) {
								if ($state) {
									return;
								}
								$set('settings.input.enclosure', '"');
							}),
			            Forms\Components\TextInput::make('settings.input.delimiter')
				            ->label('Delimiter')
				            ->afterStateHydrated(function(string|array|null $state, AbstractImport $record = null, Set $set, Get $get) {
					            if ($state) {
						            return;
					            }
					            $set('settings.input.delimiter', ',');
				            }),
			            Forms\Components\Checkbox::make('settings.input.header')
				            ->label('The first row of this file is headers.'),
			            // ...
		            ])
	            ->visible(function($get) {
					return in_array($get('mime_type'), ['text/csv', 'text/plain']);
	            })
	    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
	            Tables\Columns\TextColumn::make('media.name'),
				Tables\Columns\TextColumn::make('status')
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImports::route('/'),
            'create' => Pages\CreateImport::route('/create'),
            'edit' => Pages\EditImport::route('/{record}/edit'),
        ];
    }    
}
