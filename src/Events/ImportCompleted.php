<?php

declare(strict_types=1);

namespace Crumbls\Importer\Events;

use Crumbls\Importer\Contracts\AbstractImport;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportCompleted {

	use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * Create a new event instance.
	 */
	public function __construct(public AbstractImport $record) {
	}

}