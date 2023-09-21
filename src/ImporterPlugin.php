<?php

namespace Crumbls\Importer;

use Crumbls\Importer\Filament\Resources\ImportResource;
use Crumbls\Wellness\Filament\Resources\PositionResource;
use DanHarrin\FilamentBlog\Pages\Settings;
use DanHarrin\FilamentBlog\Resources\CategoryResource;
use DanHarrin\FilamentBlog\Resources\PostResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ImporterPlugin implements Plugin
{
	public function getId(): string
	{
		return 'importer';
	}

	public function register(Panel $panel): void
	{
		$panel
			->resources([
				ImportResource::class
			])
			->pages([]);
	}

	public function boot(Panel $panel): void
	{
		//
	}
}