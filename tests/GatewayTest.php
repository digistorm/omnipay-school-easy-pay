<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Test;

use Carbon\Carbon;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
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

        var_dump($data);exit;

        $this->assertEquals('creditCard', $data['paymentMethod']);
        $this->assertEquals('424242424242', $data['cardNumber']);
        $this->assertEquals('John Doe', $data['cardholderName']);
        $this->assertEquals('123', $data['cvn']);
        $this->assertEquals('03', $data['expiryDateMonth']);
        $this->assertEquals(Carbon::now()->addYear()->format('Y'), $data['expiryDateYear']);
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

        $this->assertInstanceOf(PurchaseRequest::class, $request);
        $this->assertSame('10.00', $request->getAmount());

        $data = $request->getData();

        $this->assertEquals('payment', $data['transactionType']);
        $this->assertEquals('10.00', $data['principalAmount']);
        $this->assertEquals('aud', $data['currency']);
        $this->assertEquals('ABC123', $data['customerNumber']);
        $this->assertEquals('456', $data['orderNumber']);
        $this->assertEquals('EFG789', $data['singleUseTokenId']);
    }

    public function testPurchaseUsingMoney(): void
    {
        $request = $this->gateway->purchase([
            'currency' => 'AUD',
            'customerNumber' => 'ABC123',
            'orderNumber' => '456',
            'singleUseTokenId' => 'EFG789',
        ]);

        $money = new Money(1000, new Currency('AUD'));

        $request->setMoney($money);

        $this->assertInstanceOf(PurchaseRequest::class, $request);
        $this->assertSame($money, $request->getAmount());
        $this->assertSame('10.00', (new DecimalMoneyFormatter(new ISOCurrencies()))->format($request->getAmount()));

        $data = $request->getData();

        $this->assertEquals('payment', $data['transactionType']);
        $this->assertEquals('10.00', $data['principalAmount']);
        $this->assertEquals('aud', $data['currency']);
        $this->assertEquals('ABC123', $data['customerNumber']);
        $this->assertEquals('456', $data['orderNumber']);
        $this->assertEquals('EFG789', $data['singleUseTokenId']);
    }
}
