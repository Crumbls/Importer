# Filament Importer
The whole goal of this package is to allow document importing.  We focus on CSVs, but will be adding XML and WPML.  
Although this can be done without Filament, we use it extensively and wanted to create a nice interface for it.  
We use a manager pattern, like Laravel's filesystem, so the system can be extended or modified. 

The whole system works in a pretty simple way:
1) Create a new entry via the Import model.  Attached document is handled by Spatie's Media Library.
2) Execute Import Job.
3) Right now, you can just use the imported data from those models.  It lives in the content.


# This is a very early version just used for brainstorming.  It is not anywhere near production ready and should be considered an early beta.
# It is the absolute first release and hasn't had a single bit of code auditing.

## Roadmap
Lots of testing.
Imports to model / migration creation.
Alerts for active user.


## Installation

You can install the package via composer:

```bash
composer require crumbls/importer
php artisan migrate
Add \Crumbls\Importer\ImporterPlugin to your Plugins in any Filament Panel Provider.
```

If you are in a multi-tenant environment and need to use something other than an unsigned big integer ( 20 ) for the key, write a migration.
You will also need to add the \Crumbls\Importer\Traits\TenantHasImports trait to your tenant model.

## Usage

```php
// Usage description here
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email chase@o2group.com instead of using the issue tracker.

## Credits

- [Chase Miller](https://github.com/crumbls)
- [All Contributors](../../contributors)

## License

The The Unlicense. Please see [License File](LICENSE.md) for more information.