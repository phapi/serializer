<?php

namespace Phapi\Serializer;

use Phapi\Contract\Di\Container;
use Phapi\Contract\Middleware\SerializerMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Phapi\Exception\BadRequest;

/**
 * Class Deserializer
 *
 * Abstract class that implements everything that the deserializer
 * middleware needs exempt two things: the deserialize method and
 * the list of mime types that the deserializer supports.
 *
 * @category Phapi
 * @package  Phapi\Deserializer
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/phapi/serializer
 */
abstract class Deserializer implements SerializerMiddleware
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
     * Create deserializer
     *
     * Pass additional mime types that the deserializer should accept
     * as valid JSON.
     *
     * @param null|array $mimeTypes
     */
    public function __construct($mimeTypes = null)
    {
        $this->mimeTypes = ($mimeTypes === null) ? $this->mimeTypes : $mimeTypes;
    }

    /**
     * Set dependency injection container
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
        $this->container['contentTypes'] = array_merge($this->container['contentTypes'], $this->mimeTypes);
    }

    /**
     * Deserialize the body to an array if the attribute "Content-Type" or if
     * an attribute does not exists and the "Content-Type" header matches one of
     * the mime types configured in the deserializer.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     * @throws BadRequest
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        // Get content mime type
        $contentType = $this->getContentType($request);

        // Check if the content type header matches this serializers mime types
        if (in_array($contentType, $this->mimeTypes)) {
            // Get the body
            $body = (string) $request->getBody();

            // Make sure body is a string and not empty
            if (is_string($body) && !empty($body)) {
                // Try to decode
                $array = $this->deserialize($body);

                // Save the deserialized body to the request
                $request = $request->withParsedBody($array);
            }
        }

        // Call next middleware and return the response
        return $next($request, $response, $next);
    }

    /**
     * Abstract method that must deserialize the body and return it
     * as an array instead. Must throw an BadRequest Exception on failure.
     *
     * @param $body
     * @return array
     * @throws BadRequest
     */
    abstract public function deserialize($body);

    /**
     * Get content type from request. First check for an attribute. An attribute
     * is usually set if content negotiation are done.
     *
     * If no attribute can be found, use the content type header.
     *
     * @param ServerRequestInterface $request
     * @return mixed|null|string
     */
    protected function getContentType(ServerRequestInterface $request)
    {
        // Check for an attribute
        if (null !== $accept = $request->getAttribute('Content-Type', null)) {
            return $accept;
        }

        // Check for an accept header
        if ($request->hasHeader('Content-Type')) {
            return $request->getHeaderLine('Content-Type');
        }

        return null;
    }
}