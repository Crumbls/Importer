# Filament Importer
The whole goal of this package is to allow document importing.  We focus on CSVs.  Although this can be done without Filament, we use it extensively and wanted to create a nice interface for it.

The system works by creating an importer, attaching said documents, then running the parser as a job against it.

This is a very early version just used for brainstorming.  It is not anywhere near production ready and should be considered an early beta.



## Installation

You can install the package via composer:

```bash
composer require crumbls/importer
php artisan migrate
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