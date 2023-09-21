<?php

namespace Crumbls\Importer\Jobs;

use Crumbls\Importer\Contracts\AbstractImport;
use Crumbls\Importer\Enums\ImportStatus;
use Crumbls\Importer\Events\ImportCompleted;
use Crumbls\Importer\Events\ImportFailed;
use Crumbls\Importer\Models\Import;
use Crumbls\Importer\Pipes\Driver;
use Crumbls\Importer\Pipes\Execute;
use Crumbls\Importer\Pipes\FileEncoding;
use Crumbls\Importer\Pipes\Localize;
use Crumbls\Importer\Pipes\Media;
use Crumbls\Importer\Pipes\MimeType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Pipeline\Pipeline;

class ProcessImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
	    #[WithoutRelations]
		public AbstractImport $model
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void {
		$manager = app('import');

	    /**
	     * We have a pretty strict way to handle this.
	     */
		try {
			$data = app(Pipeline::class)
				->send($this->model)
				->through([
					FileEncoding::class,
					Driver::class,
					Execute::class
				])
				->thenReturn()
				->get();
			$this->model->status = ImportStatus::IMPORTED->value;
			ImportCompleted::dispatch($this->model);
		} catch (\Throwable $e) {
			$this->model->status = ImportStatus::ERROR->value;
			ImportFailed::dispatch($this->model, $e);
		}
    }
}
