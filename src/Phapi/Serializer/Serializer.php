<?php

namespace Phapi\Serializer;

use Phapi\Contract\Di\Container;
use Phapi\Contract\Middleware\SerializerMiddleware;
use Phapi\Exception\InternalServerError;
use Phapi\Http\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Json
 *
 * Middleware that serializes the response body to JSON
 *
 * @category Phapi
 * @package  Phapi\Middleware\Serializer
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/phapi/serializer
 */
abstract class Serializer implements SerializerMiddleware
{

    /**
     * Valid mime types
     *
     * @var array
     */
    protected $mimeTypes = [];

    /**
     * Dependency injection container
     *
     * @var Container
     */
    protected $container;

    /**
     * Create serializer.
     *
     * Pass additional mime types that the serializer should accept
     * as valid JSON.
     *
     * @param null|array $mimeTypes
     */
    public function __construct($mimeTypes = null)
    {
        $this->mimeTypes = ($mimeTypes === null) ? $this->mimeTypes : array_merge($this->mimeTypes, $mimeTypes);
    }

    /**
     * Set the dependency injection container
     *
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register supported mime types to the container
     */
    public function registerMimeTypes()
    {
        $this->container['acceptTypes'] = array_merge($this->container['acceptTypes'], $this->mimeTypes);
    }

    /**
     * Serializes the body to a JSON string if the attribute "Accept" or if
     * an attribute does not exists and the "Accept" header matches one of
     * the mime types configured in the serializer.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     * @throws InternalServerError
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        // Call next middleware
        $response = $next($request, $response, $next);

        // Get response content type
        $contentType = $this->getContentType($response);

        // Check if the accept header matches this serializers mime types
        if (!in_array($contentType, $this->mimeTypes)) {
            // This serializer does not handle this mime type so there is nothing
            // left to do. Return response.
            return $response;
        }

        // Check if the response has a method for getting the unserialized body since
        // it's not part of the default PSR-7 implementation.
        try {
            $unserializedBody = $response->getUnserializedBody();
        } catch (\Exception $e) {
            throw new \RuntimeException('Serializer could not retrieve unserialized body');
        }

        // Check if the body is an array and not empty
        if (is_array($unserializedBody) && !empty($unserializedBody)) {
            // Try and encode the array to json
            $json = $this->serialize($unserializedBody);

            // Create a new body with the serialized content
            $body = new Stream('php://memory', 'w+');
            $body->write($json);

            // Add the body to the response
            $response = $response->withBody($body);
        }

        // Return the response
        return $response;
    }

    /**
     * Method serializing the body (array) to a string.
     *
     * @param array $unserializedBody
     * @return string
     * @throws InternalServerError
     */
    abstract public function serialize(array $unserializedBody = []);

    /**
     * Check if the request has an attribute set with a mime type that should
     * be used. This is typically a result of content negotiation. If no
     * attribute exists, check for an accept header instead.
     *
     * @param ResponseInterface $response
     * @return mixed|string
     */
    protected function getContentType(ResponseInterface $response)
    {
        // Check for an accept header
        if ($response->hasHeader('Content-Type')) {
            // Get the first part of the header, for example: exclude charset=utf-8

            $header = $response->getHeaderLine('Content-Type');
            $parts = explode(';', $header);
            return trim($parts[0]);
        }

        return null;
    }
}
