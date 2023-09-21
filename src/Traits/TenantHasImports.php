<?php

namespace Crumbls\Importer\Traits;

use Crumbls\Importer\ImporterServiceProvider;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait TenantHasImports {
	public function imports() : HasMany {
		return $this->hasMany(ImporterServiceProvider::getModelImporter());
	}
}