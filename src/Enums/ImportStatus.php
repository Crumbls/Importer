<?php

namespace Crumbls\Importer\Enums;

/**
 * TODO: Add future statuses.
 */
enum ImportStatus : string {
	case DRIVER = 'determining-driver';
	case ERROR = 'error';
	case EXECUTE = 'executing';
	case IMPORTED = 'imported';
	case PENDING = 'pending';
	case FILEENCODING = 'file-encoding';

	public static function getDefault() : string {
		return 'pending';
	}
}
