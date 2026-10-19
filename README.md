# Stylist

Stylist is a Laravel 10 package for registering, activating, and publishing application themes.

## Requirements

- PHP 8.1 or newer
- Laravel 10.x

## Installation

Currently, the package is not published to Packagist.
Fork this repo, then register a vcs repo on composer

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/{your username}/stylist"
    }
],
"require":{
    "floatingpoint/stylist": "dev-laravel-10",
}
```

Register the service provider in `config/app.php` if package discovery is disabled:

```php
'FloatingPoint\\Stylist\\StylistServiceProvider',
```

The package provides the `Stylist` and `Theme` facades.

## Theme Setup

Each theme must contain a `theme.json` file and a `views` directory. For example:

```json
{
    "name": "My theme",
    "description": "The application theme"
}
```

Register and activate a theme from a service provider or application bootstrap code:

```php
Stylist::registerPath('/absolute/path/to/theme', true);
```

Themes can also be discovered and configured through `config/stylist.php`:

```php
'themes' => [
    'paths' => [base_path('themes')],
    'activate' => 'My theme',
],
```

## Theme Assets

Use the `Theme` facade to generate URLs for assets in the active theme:

```blade
{{ Theme::style('css/app.css') }}
{{ Theme::script('js/app.js') }}
{{ Theme::image('images/logo.png') }}
{{ Theme::url('favicon.ico') }}
```

Theme assets are copied to `public/themes` with the Artisan command:

```sh
php artisan stylist:publish
php artisan stylist:publish "My theme"
```

## Theme Inheritance

A theme can inherit views and styles from another registered theme by setting its parent in
`theme.json`:

```json
{
    "name": "Child theme",
    "description": "A customized theme",
    "parent": "My theme"
}
```

The child theme takes precedence while missing views and matching stylesheets are resolved from
the parent theme.
