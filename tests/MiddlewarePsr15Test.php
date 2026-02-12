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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewarePsr15Test extends TestCase
{
    use RequestResponseStubTrait;

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
        $request = $this->createPreflightRequestStub();

        $middleware = $this->build_middleware();

        $response = $this->createPreflightResponseStub();

        $response = $middleware($request, $this->createRequestHandlerStub($response));

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
        $request = $this->createActualRequestStub();

        $middleware = $this->build_middleware();

        $response = $this->createActualResponseStub();

        $response = $middleware($request, $this->createRequestHandlerStub($response));

        $this->assertEquals('http://example.com', $response->getHeader('Access-Control-Allow-Origin')[0]);
        $this->assertEquals('true', $response->getHeader('Access-Control-Allow-Credentials')[0]);
        $this->assertEquals(['X-My-Custom-Header'], $response->getHeader('Access-Control-Expose-Headers'));
    }

    private function createRequestHandlerStub($response): RequestHandlerInterface & Stub
    {
        /** @var \Psr\Http\Server\RequestHandlerInterface & \PHPUnit\Framework\MockObject\Stub */
        $stub = $this->createStub(RequestHandlerInterface::class);
        $stub->method('handle')
            ->willReturn($response);

        return $stub;
    }
}
