# php-simple-enum

[![Coverage Status](https://coveralls.io/repos/github/evaisse/php-simple-enum/badge.svg?branch=master)](https://coveralls.io/github/evaisse/php-simple-enum?branch=master)
[![Tests Status](https://travis-ci.org/evaisse/php-simple-enum.svg?branch=master)](https://travis-ci.org/evaisse/php-simple-enum)

A Simple ENUM utils, to ensure inputs are compliant with your some basic data structs.

which allow to fetch a static list of values from givens constants : 

```php
        $enum = \evaisse\PhpSimpleEnum\PhpEnum::fromConstants('\Symfony\Component\HttpFoundation\Request::METHOD_*');
        
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
 
