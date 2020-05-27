# php-simple-enum

[![codecov](https://codecov.io/gh/evaisse/php-simple-enum/branch/master/graph/badge.svg)](https://codecov.io/gh/evaisse/php-simple-enum)
[![Tests Status](https://travis-ci.org/evaisse/php-simple-enum.svg?branch=master)](https://travis-ci.org/evaisse/php-simple-enum)

A Simple ENUM utils, to ensure inputs are compliant with your some basic data structs.

which allow to fetch a static list of values from givens constants : 

```php
    use evaisse\PhpSimpleEnum\PhpEnum;

    $enumInt = PhpEnum::fromConstants([
        'FOO' => 1,
        'BAR' => 2,
    ]);

    $enumString = PhpEnum::fromConstants([
        'FOO' => 'foo',
        'BAR' => 'bar',
    ]);

    $enum = PhpEnum::fromConstants('\Symfony\Component\HttpFoundation\Request::METHOD_*');
    
    $enum->getAllowedValues(); 
    /*
    [
        'GET', 'POST', ...
    ]
     */        

    assert($enum->getKeyForValue(\Symfony\Component\HttpFoundation\Request::METHOD_GET) === 'METHOD_GET');

    $enum->isAllowed('GET'); // true

    $enum->getHash(); /*
       [
            'METHOD_GET' => 'GET', 
            ...
        ]
    */
```
 
