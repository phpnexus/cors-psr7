<?php

/**
 * Mock request handler for test
 *
 * @link        https://github.com/phpnexus/cors-psr7
 * @copyright   Copyright (c) 2016 Mark Prosser
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */

namespace PhpNexus\CorsPsr7\Tests;

use PHPUnit\Framework\MockObject\Stub;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait RequestResponseStubTrait
{
    private function createPreflightRequestStub(): ServerRequestInterface & Stub
    {
        $request = $this->createStub(\Psr\Http\Message\ServerRequestInterface::class);

        $request->method('getMethod')
            ->willReturn('OPTIONS');

        $request->method('getHeaders')
            ->willReturn([
                'Origin' => 'http://example.com',
                'Access-Control-Request-Method' => 'PATCH',
                'Access-Control-Request-Headers' => 'Accept, Authorization, Content-Type',
            ]);

        return $request;
    }

    private function createPreflightResponseStub(): ResponseInterface & Stub
    {
        $response = $this->createStub(ResponseInterface::class);

        $response->method('getHeader')
            ->willReturnMap([
                ['Access-Control-Allow-Origin', ['http://example.com']],
                ['Access-Control-Allow-Credentials', ['true']],
                ['Access-Control-Allow-Methods', ['PATCH', 'DELETE']],
                ['Access-Control-Allow-Headers', ['Authorization,Content-Type']],
                ['Max-Age', ['3600']],
            ]);

        return $response;
    }

    private function createActualRequestStub(): ServerRequestInterface & Stub
    {
        $request = $this->createStub(\Psr\Http\Message\ServerRequestInterface::class);

        $request->method('getMethod')
            ->willReturn('PATCH');

        $request->method('getHeaders')
            ->willReturn([
                'Origin' => 'http://example.com',
            ]);

        return $request;
    }

    private function createActualResponseStub(): ResponseInterface & Stub
    {
        $response = $this->createStub(\Psr\Http\Message\ResponseInterface::class);

        $response->method('getHeader')
            ->willReturnMap([
                ['Access-Control-Allow-Origin', ['http://example.com']],
                ['Access-Control-Allow-Credentials', ['true']],
                ['Access-Control-Expose-Headers', ['X-My-Custom-Header']],
            ]);

        return $response;
    }
}
