<?php
/**
 * CORS PSR-7 middleware
 *
 * @link        https://github.com/phpnexus/cors-psr7
 * @copyright   Copyright (c) 2020 Mark Prosser
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */

namespace PhpNexus\CorsPsr7;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewarePsr7 extends Middleware
{
    /**
     * Invokable class
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param callable                                 $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        // Build CorsRequest from PSR-7 request
        $corsRequest = $this->buildCorsRequest($request);

        // If NOT preflight request; perform $next action and collect response
        if (!$corsRequest->isPreflight()) {
            $response = $next($request, $response);
        }

        // Process CorsRequest
        $corsResponse = $this->cors->process($corsRequest);

        // Apply CORS response parameters to PSR-7 response
        $response = $this->applyResponseParams($corsResponse, $response);

        return $response;
    }
}
