<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Test;

use Carbon\Carbon;
use Money\Currency;
use Money\Money;
use Omnipay\Common\CreditCard;
use Omnipay\SchoolEasyPay\Gateway;
use Omnipay\SchoolEasyPay\Message\CreateSingleUseCardTokenRequest;
use Omnipay\SchoolEasyPay\Message\PurchaseRequest;
use Omnipay\Tests\GatewayTestCase;

/**
 * @property Gateway gateway
 */
class GatewayTest extends GatewayTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true);
    }

    public function testCreateToken(): void
    {
        $request = $this->gateway->createSingleUseCardToken([
            'card' => new CreditCard([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'number' => '424242424242',
                'expiryMonth' => '03',
                'expiryYear' => Carbon::now()->addYear()->format('Y'),
                'cvv' => '123',
            ]),
        ]);

        $this->assertInstanceOf(CreateSingleUseCardTokenRequest::class, $request);
        $data = $request->getData();

        $wantedExpiry = '03/' . Carbon::now()->addYear()->format('y');

        $this->assertEquals('424242424242', $data['cardNumber']);
        $this->assertEquals('John Doe', $data['cardHolderName']);
        $this->assertEquals($wantedExpiry, $data['expiry']);
    }

    public function testPurchaseUsingStringAmount(): void
    {
        $request = $this->gateway->purchase([
            'amount' => '10.00',
            'currency' => 'AUD',
            'customerNumber' => 'ABC123',
            'orderNumber' => '456',
            'singleUseTokenId' => 'EFG789',
        ]);

        $card = new CreditCard([
            'firstName' => 'Bobby',
            'lastName' => 'Tables',
            'number' => '4444333322221111',
            'cvv' => '123',
            'expiryMonth' => '12',
            'expiryYear' => '2017',
            'email' => 'testcard@gmail.com',
        ]);

        $request->setCard($card);

        $this->assertInstanceOf(PurchaseRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());

        $request->setCardProxy('bdd4c12345e54b00b4e1a2b309442a07');
        $request->setCustomerReference('ABC123');

        $data = $request->getData();

        $this->assertEquals('bdd4c12345e54b00b4e1a2b309442a07', $data['cardProxy']);
        $this->assertEquals('ABC123', $data['customerReference']);
        $this->assertEquals('Bobby Tables', $data['customerName']);
        $this->assertEquals('10.00', $data['paymentAmount']);
    }

    public function testPurchaseUsingMoney(): void
    {
        $card = new CreditCard($this->getValidCard());
        $request = $this->gateway->purchase([
            'currency' => 'AUD',
            'cardProxy' => 'bdd4c12345e54b00b4e1a2b309442a07',
            'customerReference' => 'ABC123',
            'card' => $card,
        ]);

        $name = $card->getFirstName() . ' ' . $card->getLastName();
        $money = new Money(1000, new Currency('AUD'));

        $request->setMoney($money);

        $this->assertInstanceOf(PurchaseRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());

        $data = $request->getData();

        $this->assertEquals('10.00', $data['paymentAmount']);
        $this->assertEquals('bdd4c12345e54b00b4e1a2b309442a07', $data['cardProxy']);
        $this->assertEquals('ABC123', $data['customerReference']);
        $this->assertEquals($name, $data['customerName']);
    }
}
