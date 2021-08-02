<?php
/**
 * CORS PSR-7 middleware test
 *
 * @link        https://github.com/phpnexus/cors-psr7
 * @copyright   Copyright (c) 2016 Mark Prosser
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */

namespace PhpNexus\CorsPsr7\Tests;

use PhpNexus\Cors\CorsService;
use PhpNexus\CorsPsr7\MiddlewarePsr15 as CorsPsr15Middleware;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\ServerRequest;

class MiddlewarePsr15Test extends TestCase
{
    /**
     * @return CorsPsr15Middleware
     */
    public function build_middleware()
    {
        $corsService = new CorsService([
            'allowMethods'     => ['PATCH', 'DELETE'],
            'allowHeaders'     => ['Authorization', 'Content-Type'],
            'allowOrigins'     => ['http://example.com'],
            'allowCredentials' => true,
            'exposeHeaders'    => ['X-My-Custom-Header'],
            'maxAge'           => 3600,
        ]);

        return new CorsPsr15Middleware($corsService);
    }

    /**
     * Test response headers are set from preflight request
     */
    public function test_preflight_request()
    {
        $request = (new ServerRequest)
            ->withMethod('OPTIONS')
            ->withHeader('Origin', 'http://example.com')
            ->withHeader('Access-Control-Request-Method', 'PATCH')
            ->withHeader('Access-Control-Request-Headers', 'Accept, Authorization, Content-Type')
        ;

        $middleware = $this->build_middleware();

        $response = $middleware($request, new MockRequestHandler);

        $this->assertEquals('http://example.com', $response->getHeader('Access-Control-Allow-Origin')[0]);
        $this->assertEquals('true', $response->getHeader('Access-Control-Allow-Credentials')[0]);
        $this->assertEquals(['PATCH', 'DELETE'], $response->getHeader('Access-Control-Allow-Methods'));
        $this->assertEquals(['Authorization,Content-Type'], $response->getHeader('Access-Control-Allow-Headers'));
        $this->assertEquals('3600', $response->getHeader('Max-Age')[0]);
    }

    /**
     * Test response headers are set from actual request
     */
    public function test_actual_request()
    {
        $request = (new ServerRequest)
            ->withMethod('PATCH')
            ->withHeader('Origin', 'http://example.com')
        ;

        $middleware = $this->build_middleware();

        $response = $middleware($request, new MockRequestHandler);

        $this->assertEquals('http://example.com', $response->getHeader('Access-Control-Allow-Origin')[0]);
        $this->assertEquals('true', $response->getHeader('Access-Control-Allow-Credentials')[0]);
        $this->assertEquals(['X-My-Custom-Header'], $response->getHeader('Access-Control-Expose-Headers'));
    }
}
