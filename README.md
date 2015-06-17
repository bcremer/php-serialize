# PHP Object serializer

POC userland implementation of PHP´s inbuilt `serialize`/`unserialize`.


## Installation

```
composer require bcremer/serialize
```

## Usage

```php
$instantiator = new \Doctrine\Instantiator\InstantiatorInstantiator();
$serializer = new \Bcremer\Serialize\Serializer\Serializer($instantiator);

$object = new \Bcremer\Serialize\Tests\Objects\SimpleObject("fooValue", "barValue");
$serializedObject = $serializer->serialize($object);

/*
Array
(
    [className] => Bcremer\Serialize\Tests\Objects\SimpleObject
    [properties] => Array
        (
            [foo] => fooValue
            [bar] => barValue
        )

)
*/

$restoredObject = $serializer->unserialize($serializedObject);

assert($restoredObject == $object);
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
