<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Test\Message;

use Omnipay\Common\CreditCard;
use Omnipay\SchoolEasyPay\Message\PurchaseRequest;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /**
     * @var PurchaseRequest
     */
    protected $request;

    public function setUp(): void
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData(): void
    {
        $card = new CreditCard($this->getValidCard());
        $this->request->setCardProxy('bdd4c12345e54b00b4e1a2b309442a07');
        $this->request->setAmount(12.50);
        $this->request->setCustomerReference('ABC123');
        $this->request->setCard($card);

        $data = $this->request->getData();

        $this->assertEquals('bdd4c12345e54b00b4e1a2b309442a07', $data['cardProxy']);
        $this->assertEquals('12.50', $data['paymentAmount']);
        $this->assertEquals('ABC123', $data['customerReference']);
    }
}
