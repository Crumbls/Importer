<?php

namespace Crumbls\Importer\Facades;

use Illuminate\Support\Facades\Facade;

class Import extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'importer';
	}
}