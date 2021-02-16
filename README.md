# Vodafone MSISDN detect package

## Installation

###### Install package
```
$ composer require arendach/vodafone-name
```

###### Add Service provider to app.providers configs
```php
\Arendach\VodafoneName\Providers\NameServiceProvider::class,
```

###### Publish configs
```
$ php artisan vendor:publish --tag=vodafone-name
```


> Configuration .env file

```ini
# Middleware
NAME_MIDDLEWARE_HOST='<string: Middleware host, default: https://dspmw.vodafone.ua>'
NAME_MIDDLEWARE_LOGIN='<string: Middleware auth login>'
NAME_MIDDLEWARE_PASSWORD='<string: Middleware auth password>'
NAME_MIDDLEWARE_PROFILE='<string: Middleware name profile, default: NAME-RTM>'
NAME_MIDDLEWARE_CHANNEL='<string: Middleware channel, default: VF-WEBSITE>'
# Other
NAME_TESTING_MODE='<bool: enable | disable fake names for local testing>'
NAME_DEBUG_MODE='<bool: enable | disable logging for name service>'
```

## How to use

```php
$nameService = new \Arendach\VodafoneName\Name();

$name = $nameService->search('380666817731'); // return name or null
// or
$nameService->searchAndSave('380666817731'); // return name or null

$nameService->getName(); // name from session
$nameService->getStatus(); // status of search name: -1 | 1
```