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

    public function testApiKeyPublic(): void
    {
        $this->assertSame($this->request, $this->request->setApiKeyPublic('abc123'));
        $this->assertSame('abc123', $this->request->getApiKeyPublic());
    }

    public function testApiKeySecret(): void
    {
        $this->assertSame($this->request, $this->request->setApiKeySecret('abc123'));
        $this->assertSame('abc123', $this->request->getApiKeySecret());
    }

    public function testMerchantId(): void
    {
        $this->assertSame($this->request, $this->request->setMerchantId('abc123'));
        $this->assertSame('abc123', $this->request->getMerchantId());
    }

    public function testUseSecretKey(): void
    {
        $this->assertSame($this->request, $this->request->setUseSecretKey('abc123'));
        $this->assertSame('abc123', $this->request->getUseSecretKey());
    }

    public function testSingleUseTokenId(): void
    {
        $this->assertSame($this->request, $this->request->setSingleUseTokenId('abc123'));
        $this->assertSame('abc123', $this->request->getSingleUseTokenId());
    }

    public function testIdempotencyKey(): void
    {
        $this->assertSame($this->request, $this->request->setIdempotencyKey('abc123'));
        $this->assertSame('abc123', $this->request->getIdempotencyKey());
    }
}
