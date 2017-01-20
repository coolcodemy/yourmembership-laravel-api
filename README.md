# YourMembership-Laravel-API



### This package in a work in progress, it is based on the code [YM-API](https://github.com/phone2action/ym-api).

This package implements a PHP wrapper to work with http://www.yourmembership.com/company/api-reference/


### Laravel Installation (5.2+)


Require this package with composer by adding the following to your composer file:

```json
"require": {
    "coolcodemy/yourmembership-laravel-api": "^0.0.1"
},
```

Then run `composer update` to download the package.

**Or use**

```bash
composer require coolcodemy/yourmembership-laravel-api
```


After updating composer, add the service provider to the `providers` array in `config/app.php`

```php
CoolCodeMY\YourMembershipLaravelAPI\YourMembershipServiceProvider::class,
```

Publish the config for the package
```bash
php artisan vendor:publish --provider="CoolCodeMY\YourMembershipLaravelAPI\YourMembershipServiceProvider"
```

Fill in your **API_KEY**, **SECRET_API_KEY** and **SA_PASSCODE** inside `config/yourmembership-laravel-api.php`.


### Usage

```php
<?php
...
use CoolCodeMY\YourMembershipLaravelAPI\YMLA;

class YourController extends Controller {

    public function index(YMLA $ymla)
    {
        // Array results
        $result = $ymla->call('Auth.Authenticate', [
            'Username' => 'email@examle.com',
            'Password' => 'password',
        ])->toArray();
        
        // JSON/Object result
        $result = $ymla->call('Auth.Authenticate', [
            'Username' => 'email@examle.com',
            'Password' => 'password',
        ])->toJson();
        
        // XML result
        $result = $ymla->call('Auth.Authenticate', [
            'Username' => 'email@examle.com',
            'Password' => 'password',
        ])->toXML();
    }
    ...
}
```
#### Notes on YourMembership session
You don't need to generate session for authentication as this package will do it for you. The **SessionID** is saved inside Laravel cache for 15 minutes.


####  Returning the RAW XML results from YourMembership
This will return the results as XML and set the return header to XML for the browser to properly print valid XML.
```php
...
$result = $ymla->call('Auth.Authenticate', [
    'Username' => 'email@examle.com',
    'Password' => 'password',
])->toXML();

return response()->xml($result);
...
```


