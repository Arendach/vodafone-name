# Vodafone MSISDN detect package

## Installation

###### Install package
```
$ composer require arendach/vodafone-name
```
###### Publish configs
```
$ php artisan vendor:publish --tag=vodafone-name
```

## Logging

> If you need to save logs, then add the «msisdn» channel to the config/logging.php file

```php

...

'msisdn' => [
    'driver' => 'daily',
    'path'   => storage_path('logs/name.log'),
    'level'  => 'debug',
],

...

```
> and set var in to .env file

```ini
NAME_DEBUG_MODE=true
```

## How to use

```php



```