# php-simple-enum


A Simple ENUM utils


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
 
