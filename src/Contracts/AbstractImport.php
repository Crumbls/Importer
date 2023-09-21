<?php

namespace Crumbls\Importer\Contracts;

use Crumbls\Importer\Enums\ImportStatus;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

abstract class AbstractImport extends Model implements HasMedia {
	use InteractsWithMedia;

	/**
	 * @var null[]
	 */
	protected $attributes = [
		'driver' => null,
		'status' => null,
	];

	/**
	 * @var string[]
	 */
	protected $fillable = [
		'driver',
		'status',
		'settings',
		'content'
	];

	/**
	 * @var string[]
	 */
	protected $casts = [
		'settings' => 'array',
		'content' => 'array',
	];

	public static function boot() : void {
		parent::boot();
		static::creating(function($model) {
			$model->status;

			$tenant = Filament::getTenant();
			if ($tenant) {
				$model->tenant()->associate($tenant);
			}
		});
	}
}