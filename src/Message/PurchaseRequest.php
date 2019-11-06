<?php

namespace Omnipay\SchoolEasyPay\Message;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

/**
 * @link https://www.payway.com.au/rest-docs/index.html#process-a-payment
 */
class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate(
            'cardProxy',
            'customerReference',
            'amount'
        );

        $data = [
            'cardProxy' => $this->getCardProxy(),
            'customerReference' => $this->getCustomerReference(),
        ];

        if ($this->getCard()) {
            $card = $this->getCard();
            $data['customerName'] = $card->getName();
        }

        // Has the Money class been used to set the amount?
        if ($this->getAmount() instanceof Money) {
            // Ensure principal amount is formatted as decimal string
            $data['paymentAmount'] = (new DecimalMoneyFormatter(new ISOCurrencies()))->format($this->getAmount());
        } else {
            $data['paymentAmount'] = $this->getAmount();
        }

        if ($this->getMerchantUniquePaymentId()) {
            $data['merchantUniquePaymentId'] = $this->getMerchantUniquePaymentId();
        }

        return $data;
    }

    public function getCardProxy()
    {
        return $this->getParameter('cardProxy');
    }

    public function setCardProxy($value)
    {
        return $this->setParameter('cardProxy', $value);
    }

    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    public function getMerchantUniquePaymentId()
    {
        return $this->getParameter('merchantUniquePaymentId');
    }

    public function setMerchantUniquePaymentId($value)
    {
        return $this->setParameter('merchantUniquePaymentId', $value);
    }

    public function getEndpoint()
    {
        return $this->getBaseEndpoint() . '/payments';
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function getUseSecretKey()
    {
        return true;
    }

    /**
     * @param $data
     * @return \Omnipay\SchoolEasyPay\Message\PurchaseResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
