# Serializer Helper Class
The serializer and deserializer helper classes implements a majority of the [SerializerMiddleware Contract](https://github.com/phapi/contract/blob/master/src/Phapi/Contract/Middleware/SerializerMiddleware.php).

Each serializer package should include both a Serializer and a Deserializer. You need to implement two parts for each of them:

- List supported mime types
- Implement the abstract serializer/deserializer method. Note that the method should throw an InternalServerError if the serialize/deserialize fails.

```php
<?php

namespace Phapi\Middleware\Serializer\Example;

use Phapi\Exception\InternalServerError;
use Phapi\Serializer\Serializer;

class Example extends Serializer
{

    /**
     * Valid mime types
     *
     * @var array
     */
    protected $mimeTypes = [
        'application/example'
    ];

    /**
     * Serialize body to json
     *
     * @param array $unserializedBody
     * @return string
     * @throws InternalServerError
     */
    protected function serialize(array $unserializedBody = [])
    {
    }
}
```

## Example:
See the Json [serializer](https://github.com/phapi/serializer-json/blob/master/src/Phapi/Middleware/Serializer/Json/Json.php) and [deserializer](https://github.com/phapi/serializer-json/blob/master/src/Phapi/Middleware/Deserializer/Json/Json.php) for a working example.

## Phapi
This is a Phapi package used by the [Phapi Framework](https://github.com/phapi/phapi).

## License
Serializer is licensed under the MIT License - see the [license.md](https://github.com/phapi/serializer/blob/master/license.md) file for details

## Contribute
Contribution, bug fixes etc are [always welcome](https://github.com/phapi/serializer/issues/new).
