<?php

namespace Crumbls\Importer\Models;

use Crumbls\Importer\Contracts\AbstractImport;
use Crumbls\Importer\Exceptions\TenantsNotSupported;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 */
class Import extends AbstractImport
{
	/**
	 * Lazy way to infill the status.
	 * @return string
	 */
	public function getStatusAttribute() : string {
		if (!array_key_exists('status', $this->attributes) || !$this->attributes['status']) {
			$this->attributes['status'] = \Crumbls\Importer\Enums\ImportStatus::getDefault();
		}
		return $this->attributes['status'];
	}

	/**
	 * Status attributes always update. Not the best way to do it since 8.x, but here we are.
	 * @param string $input
	 */
	public function setStatusAttribute(string $input): void
	{
		if (!$this->exists) {
			return;
		}

		if (array_key_exists('status', $this->attributes) && $this->attributes['status'] == $input) {
			return;
		}

		/**
		 * Force set.
		 */
		$this->attributes['status'] = $input;

		/**
		 * This can be done much more eloquently.
		 */
		$this->getConnection()
			->table($this->getTable())
			->where($this->getKeyName(), $this->getKey())
			->update(['status' => $this->attributes['status']]);
	}


	/**
	 * Optional belongs to relationship for a tenant.
	 * Just working on some future work.
	 * @return BelongsTo
	 * @throws TenantsNotSupported
	 */
	public function tenant() : BelongsTo {
		$panel = Filament::getPanel();
		$model = $panel->getTenantModel();
		if (!$model) {
			throw new TenantsNotSupported();
		}
		return $this->belongsTo($model);
	}
}