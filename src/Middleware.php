<?php
/**
 * CORS PSR-7 middleware
 *
 * @link        https://github.com/phpnexus/cors-psr7
 * @copyright   Copyright (c) 2020 Mark Prosser
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */

namespace PhpNexus\CorsPsr7;

use PhpNexus\Cors\CorsRequest;
use PhpNexus\Cors\CorsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Middleware
{
    /** @var \PhpNexus\Cors\CorsService */
    private $cors;

    /**
     * @param \PhpNexus\Cors\CorsService $cors
     */
    public function __construct(CorsService $cors)
    {
        $this->cors = $cors;
    }

    /**
     * Invokable class
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param callable                                $next
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

    /**
     * Build CorsRequest from PSR-7 ServerRequest object
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \PhpNexus\Cors\CorsRequest
     */
    protected function buildCorsRequest(ServerRequestInterface $request): CorsRequest
    {
        // Create CorsRequest and set method
        $corsRequest = (new CorsRequest)
            ->setMethod($request->getMethod());

        // Set Origin if header exists
        if ($request->hasHeader('Origin')) {
            $corsRequest->setOrigin($request->getHeader('Origin')[0]);
        }

        // Set access control request method if header exists
        if ($request->hasHeader('Access-Control-Request-Method')) {
            $corsRequest->setAccessControlRequestMethod($request->getHeader('Access-Control-Request-Method')[0]);
        }

        // Set access control request headers if header exists
        if ($request->hasHeader('Access-Control-Request-Headers')) {
            /** Not sure if this is a bug in the Slim implementation of PSR-7, but it doesn't seem to separate headers properly */
            $accessControlRequestHeaders = $request->getHeader('Access-Control-Request-Headers');
            // If only one array element; explode manually
            if (count($accessControlRequestHeaders) === 1) {
                // If string contains a space; explode by comma & space
                if (strpos($accessControlRequestHeaders[0], ' ') !== false) {
                    $accessControlRequestHeaders = explode(', ', $accessControlRequestHeaders[0]);
                }
                // Else; explode by comma
                else {
                    $accessControlRequestHeaders = explode(',', $accessControlRequestHeaders[0]);
                }
            }
            $corsRequest->setAccessControlRequestHeaders($accessControlRequestHeaders);
        }

        return $corsRequest;
    }

    /**
     * Apply parameters from CORS response to PSR-7 Response object
     *
     * @param array $corsResponse
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function applyResponseParams(array $corsResponse, ResponseInterface $response): ResponseInterface
    {
        // Set Access-Control-Allow-Credentials header if appropriate
        if (isset($corsResponse['access-control-allow-credentials'])) {
            $response = $response->withHeader(
                'Access-Control-Allow-Credentials',
                $corsResponse['access-control-allow-credentials']
            );
        }

        // Set Access-Control-Allow-Headers header if appropriate
        if (isset($corsResponse['access-control-allow-headers'])) {
            $response = $response->withHeader(
                'Access-Control-Allow-Headers',
                implode(',', $corsResponse['access-control-allow-headers'])     // Have to send this header as one comma-separated line due to IE bug
            );
        }

        // Set Access-Control-Allow-Methods header if appropriate
        if (isset($corsResponse['access-control-allow-methods'])) {
            $response = $response->withHeader(
                'Access-Control-Allow-Methods',
                $corsResponse['access-control-allow-methods']
            );
        }

        // Set Access-Control-Allow-Origin header if appropriate
        if (isset($corsResponse['access-control-allow-origin'])) {
            $response = $response->withHeader(
                'Access-Control-Allow-Origin',
                $corsResponse['access-control-allow-origin']
            );
        }

        // Set Access-Control-Expose-Headers header if appropriate
        if (isset($corsResponse['access-control-expose-headers'])) {
            $response = $response->withHeader(
                'Access-Control-Expose-Headers',
                $corsResponse['access-control-expose-headers']
            );
        }

        // Set Max-Age header if appropriate
        if (isset($corsResponse['max-age'])) {
            $response = $response->withHeader(
                'Max-Age',
                $corsResponse['max-age']
            );
        }

        return $response;
    }
}
