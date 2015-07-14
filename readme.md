# Serializer Helper Class

[![Build status](https://img.shields.io/travis/phapi/serializer.svg?style=flat-square)](https://travis-ci.org/phapi/serializer)
[![Code Climate](https://img.shields.io/codeclimate/github/phapi/serializer.svg?style=flat-square)](https://codeclimate.com/github/phapi/serializer)
[![Test Coverage](https://img.shields.io/codeclimate/coverage/github/phapi/serializer.svg?style=flat-square)](https://codeclimate.com/github/phapi/serializer/coverage)

The serializer and deserializer helper classes implements a majority of the [SerializerMiddleware Contract](https://github.com/phapi/contract/blob/master/src/Phapi/Contract/Middleware/SerializerMiddleware.php).

Each serializer middleware package should always contain two classes: a serializer and a deserializer.

You can dramatically shorten the amount of time and code by using the [Phapi Serializer Helper](https://github.com/phapi/serializer) classes.

```shell
$ php composer.phar require phapi/serializer:1.*
```

These classes contains a wast majority of the needed code for a serializer. Since there is only two things that is different between different serializers there is no point in writing the same code over and over again in each serializer.

The two things that separates serializers are:

- A list of supported mime types
- The <code>serialize()</code> or <code>deserialize()</code> method. Note that the method should throw an InternalServerError if the serialize/deserialize fails.

Here's an example that can be used as a starting point for a new serializer:

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
     * Serialize body
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
This is a Phapi package used by the [Phapi Framework](https://github.com/phapi/phapi-framework).

## License
Serializer is licensed under the MIT License - see the [license.md](https://github.com/phapi/serializer/blob/master/license.md) file for details

## Contribute
Contribution, bug fixes etc are [always welcome](https://github.com/phapi/serializer/issues/new).
