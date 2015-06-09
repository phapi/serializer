<?php

namespace Phapi\Tests\Deserializer;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Serializer\Deserializer
 */
class DeserializerTest extends TestCase
{

    public function testConstruct()
    {
        // Serializer
        $deserializer = \Mockery::mock('Phapi\Serializer\Deserializer[deserialize]', [['application/json', 'text/json']]);
        $deserializer->shouldReceive('deserialize')->with("{ 'username': 'phapi' }")->andReturn(['username' => 'phapi']);

        // Container
        $container = \Mockery::mock('Phapi\Contract\Di\Container');
        $container->shouldReceive('offsetGet')->with('contentTypes')->andReturn([]);
        $container->shouldReceive('offsetSet')->with('contentTypes', ['application/json', 'text/json']);
        $deserializer->setContainer($container);

        $deserializer->registerMimeTypes();

        // Request
        $request = \Mockery::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getAttribute')->with('Content-Type', null)->andReturn('application/json');
        $request->shouldReceive('getBody')->andReturn("{ 'username': 'phapi' }");
        $request->shouldReceive('withParsedBody')->with([ 'username' => 'phapi' ])->andReturnSelf();

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');

        $deserializer($request, $response, function ($request, $response) {
            return $response;
        });
    }

    public function testWithHeader()
    {
        // Serializer
        $deserializer = \Mockery::mock('Phapi\Serializer\Deserializer[deserialize]', [['application/json', 'text/json']]);
        $deserializer->shouldReceive('deserialize')->with("{ 'username': 'phapi' }")->andReturn(['username' => 'phapi']);

        // Container
        $container = \Mockery::mock('Phapi\Contract\Di\Container');
        $container->shouldReceive('offsetGet')->with('contentTypes')->andReturn([]);
        $container->shouldReceive('offsetSet')->with('contentTypes', ['application/json', 'text/json']);
        $deserializer->setContainer($container);

        $deserializer->registerMimeTypes();

        // Request
        $request = \Mockery::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getAttribute')->with('Content-Type', null)->andReturn(null);
        $request->shouldReceive('hasHeader')->with('Content-Type')->andReturn(true);
        $request->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');
        $request->shouldReceive('getBody')->andReturn("{ 'username': 'phapi' }");
        $request->shouldReceive('withParsedBody')->with([ 'username' => 'phapi' ])->andReturnSelf();

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');

        $deserializer($request, $response, function ($request, $response) {
            return $response;
        });
    }

    public function testNoAttributeNoHeader()
    {
        // Serializer
        $deserializer = \Mockery::mock('Phapi\Serializer\Deserializer[deserialize]', [['application/json', 'text/json']]);
        $deserializer->shouldReceive('deserialize')->with("{ 'username': 'phapi' }")->andReturn(['username' => 'phapi']);

        // Container
        $container = \Mockery::mock('Phapi\Contract\Di\Container');
        $container->shouldReceive('offsetGet')->with('contentTypes')->andReturn([]);
        $container->shouldReceive('offsetSet')->with('contentTypes', ['application/json', 'text/json']);
        $deserializer->setContainer($container);

        $deserializer->registerMimeTypes();

        // Request
        $request = \Mockery::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getAttribute')->with('Content-Type', null)->andReturn(null);
        $request->shouldReceive('hasHeader')->with('Content-Type')->andReturn(false);

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');

        $deserializer($request, $response, function ($request, $response) {
            return $response;
        });
    }
}
