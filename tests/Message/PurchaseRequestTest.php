<?php

namespace Omnipay\SchoolEasyPay\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\SchoolEasyPay\Message\PurchaseRequest;

class PurchaseRequestTest extends TestCase
{
    /**
     * @var \Omnipay\SchoolEasyPay\Message\PurchaseRequest
     */
    protected $request;

    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $this->request->setCardProxy('bdd4c12345e54b00b4e1a2b309442a07');
        $this->request->setAmount(12.50);
        $this->request->setCustomerReference('ABC123');

        $data = $this->request->getData();

        $this->assertEquals('bdd4c12345e54b00b4e1a2b309442a07', $data['cardProxy']);
        $this->assertEquals('12.50', $data['paymentAmount']);
        $this->assertEquals('ABC123', $data['customerReference']);
    }
}
