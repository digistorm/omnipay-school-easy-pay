<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 18/06/17
 * Time: 21:30
 */

namespace Omnipay\SchoolEasyPay\Test\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\SchoolEasyPay\Message\CreateSingleUseCardTokenRequest;
use Omnipay\Tests\TestCase;

class CreateSingleUseCardTokenRequestTest extends TestCase
{
    private CreateSingleUseCardTokenRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new CreateSingleUseCardTokenRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testEndpoint(): void
    {
        $this->assertSame('https://api.schooleasypay.com.au/v2/cardproxies', $this->request->getEndpoint());
    }

    public function testGetDataInvalid(): void
    {
        $this->expectExceptionMessage('You must pass a "card" parameter.');
        $this->expectException(InvalidRequestException::class);
        $this->request->setCard(null);

        $this->request->getData();
    }

    public function testGetDataWithCard(): void
    {
        $card = new CreditCard($this->getValidCard());
        $this->request->setCard($card);

        $data = $this->request->getData();

        $expiryMonth = sprintf('%02d', $card->getExpiryMonth());
        $expiryYear = substr((string) $card->getExpiryYear(), 2);
        $expiry = "{$expiryMonth}/{$expiryYear}";
        $name = $card->getFirstName() . ' ' . $card->getLastName();

        $this->assertEquals($card->getNumber(), $data['cardNumber']);
        $this->assertEquals($name, $data['cardHolderName']);
        $this->assertEquals($expiry, $data['expiry']);
    }
}
