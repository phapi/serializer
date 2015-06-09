<?php

namespace Phapi\Tests\Serializer;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Serializer\Serializer
 */
class SerializerTest extends TestCase
{

    public function testConstruct()
    {
        // Serializer
        $serializer = \Mockery::mock('Phapi\Serializer\Serializer[serialize]', [['application/json', 'text/json']]);
        $serializer->shouldReceive('serialize')->with(['username' => 'phapi'])->andReturn("{ 'username': 'phapi' }");

        // Container
        $container = \Mockery::mock('Phapi\Contract\Di\Container');
        $container->shouldReceive('offsetGet')->with('acceptTypes')->andReturn([]);
        $container->shouldReceive('offsetSet')->with('acceptTypes', ['application/json', 'text/json']);
        $serializer->setContainer($container);

        $serializer->registerMimeTypes();

        // Request
        $request = \Mockery::mock('Psr\Http\Message\ServerRequestInterface');

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('hasHeader')->with('Content-Type')->andReturn(true);
        $response->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');
        $response->shouldReceive('getUnserializedBody')->andReturn(['username' => 'phapi']);
        $response->shouldReceive('withBody')->with(\Mockery::type('Psr\Http\Message\StreamInterface'))->andReturnSelf();

        $serializer($request, $response, function ($request, $response) {
            return $response;
        });
    }

    public function testNoContentType()
    {
        // Serializer
        $serializer = \Mockery::mock('Phapi\Serializer\Serializer[serialize]', [['application/json', 'text/json']]);
        $serializer->shouldReceive('serialize')->with(['username' => 'phapi'])->andReturn("{ 'username': 'phapi' }");

        // Container
        $container = \Mockery::mock('Phapi\Contract\Di\Container');
        $container->shouldReceive('offsetGet')->with('acceptTypes')->andReturn([]);
        $container->shouldReceive('offsetSet')->with('acceptTypes', ['application/json', 'text/json']);
        $serializer->setContainer($container);

        $serializer->registerMimeTypes();

        // Request
        $request = \Mockery::mock('Psr\Http\Message\ServerRequestInterface');

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('hasHeader')->with('Content-Type')->andReturn(false);
        //$response->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');
        //$response->shouldReceive('getUnserializedBody')->andReturn([ 'username' => 'phapi' ]);
        //$response->shouldReceive('withBody')->with(\Mockery::type('Psr\Http\Message\StreamInterface'))->andReturnSelf();

        $serializer($request, $response, function ($request, $response) {
            return $response;
        });
    }

    public function testNoUnserializedBodyMethod()
    {
        // Serializer
        $serializer = \Mockery::mock('Phapi\Serializer\Serializer[serialize]', [['application/json', 'text/json']]);
        $serializer->shouldReceive('serialize')->with(['username' => 'phapi'])->andReturn("{ 'username': 'phapi' }");

        // Container
        $container = \Mockery::mock('Phapi\Contract\Di\Container');
        $container->shouldReceive('offsetGet')->with('acceptTypes')->andReturn([]);
        $container->shouldReceive('offsetSet')->with('acceptTypes', ['application/json', 'text/json']);
        $serializer->setContainer($container);

        $serializer->registerMimeTypes();

        // Request
        $request = \Mockery::mock('Psr\Http\Message\ServerRequestInterface');

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('hasHeader')->with('Content-Type')->andReturn(true);
        $response->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');
        //$response->shouldReceive('getUnserializedBody')->andReturn([ 'username' => 'phapi' ]);
        //$response->shouldReceive('withBody')->with(\Mockery::type('Psr\Http\Message\StreamInterface'))->andReturnSelf();

        $this->setExpectedException('\RuntimeException', 'Serializer could not retrieve unserialized body');
        $serializer($request, $response, function ($request, $response) {
            return $response;
        });
    }
}
