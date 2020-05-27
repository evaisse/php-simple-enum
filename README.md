# php-simple-enum

[![codecov](https://codecov.io/gh/evaisse/php-simple-enum/branch/master/graph/badge.svg)](https://codecov.io/gh/evaisse/php-simple-enum)
[![Build Status](https://travis-ci.org/evaisse/php-simple-enum.svg?branch=master)](https://travis-ci.org/evaisse/php-simple-enum)

A Simple ENUM utils, to ensure inputs are compliant with your some basic data structs.

which allow to fetch a static list of values from givens constants : 

```php
use evaisse\PhpSimpleEnum\PhpEnum;

$enumInt = new PhpEnum([
    'FOO' => 1,
    'BAR' => 2,
]);

$enum = PhpEnum::fromConstants('\Symfony\Component\HttpFoundation\Request::METHOD_*');

$enum->getAllowedValues(); 
/*
[
    'GET', 'POST', ...
]
 */        

/*
 * Fetch enum name for a given value
 */
assert($enum->getKeyForValue(\Symfony\Component\HttpFoundation\Request::METHOD_GET) === 'METHOD_GET');

/*
 * test value
 */
$enum->isAllowed('GET'); // true

/*
 * fetch and assign, or throw invalid argument exception
 */
$param = $enum->validate($_GET['askedValue']);

/*
 * Fetch the whole dictonary
 */
$enum->getHash(); /*
   [
        'METHOD_GET' => 'GET', 
        ...
    ]
*/
```
 
