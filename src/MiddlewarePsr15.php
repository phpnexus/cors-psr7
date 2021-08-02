<?php
/**
 * CORS PSR-15 compliant middleware
 *
 * Includes support for __invoke() for use in Slim 4
 *
 * @link        https://github.com/phpnexus/cors-psr7
 * @copyright   Copyright (c) 2020 Mark Prosser
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */

namespace PhpNexus\CorsPsr7;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewarePsr15 extends Middleware implements MiddlewareInterface
{
    /**
     * Process
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Handle request
        $response = $handler->handle($request);

        // Build CorsRequest from PSR-7 request
        $corsRequest = $this->buildCorsRequest($request);

        // Process CorsRequest
        $corsResponse = $this->cors->process($corsRequest);

        // Apply CORS response parameters to PSR-7 response
        $response = $this->applyResponseParams($corsResponse, $response);

        return $response;
    }

    /**
     * Invokable class
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->process($request, $handler);
    }
}
