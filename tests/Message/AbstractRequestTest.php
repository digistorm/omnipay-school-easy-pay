<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Test\Message;

use Mockery;
use Omnipay\SchoolEasyPay\Message\AbstractRequest;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    public $request;

    public function setUp(): void
    {
        $this->request = Mockery::mock(AbstractRequest::class)->makePartial();
        $this->request->initialize();
    }

    /**
     * @dataProvider getSetDataProvider
     */
    public function testGetSet(string $subject): void
    {
        $getMethod = 'get' . $subject;
        $setMethod = 'set' . $subject;
        $this->assertTrue(method_exists($this->request, $getMethod));
        $this->assertTrue(method_exists($this->request, $setMethod));
        $this->assertSame($this->request, $this->request->$setMethod('abc123'));
        $this->assertSame('abc123', $this->request->$getMethod());
    }

    public static function getSetDataProvider(): array
    {
        return [
            'ApiKey' => ['ApiKey'],
            'Username' => ['Username'],
            'Password' => ['Password'],
            'IdempotencyKey' => ['IdempotencyKey'],
        ];
    }

    public function testGetBaseEndpointInTestMode(): void
    {
        $this->request->setTestMode(true);

        $this->assertEquals('https://apiuat.schooleasypay.com.au/v2', $this->request->getBaseEndpoint());
    }

    public function testGetBaseEndpointInLiveMode(): void
    {
        $this->request->setTestMode(false);

        $this->assertEquals('https://api.schooleasypay.com.au/v2', $this->request->getBaseEndpoint());
    }

    public function testGetIdempotencyKeyWhenNotSet(): void
    {
        $this->assertNotEmpty($this->request->getIdempotencyKey());
    }

    public function testGetRequestHeaders(): void
    {
        $headers = $this->request->getRequestHeaders();

        $this->assertArrayHasKey('Accept', $headers);
        $this->assertEquals('application/json', $headers['Accept']);
        $this->assertArrayNotHasKey('Content-Type', $headers);
    }
}
