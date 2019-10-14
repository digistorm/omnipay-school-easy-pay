<?php

namespace Omnipay\SchoolEasyPay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * @link https://www.payway.com.au/docs/rest.html#tokenise-a-credit-card
 */
class CreateSingleUseCardTokenRequest extends AbstractRequest
{
    public function getData()
    {
        if (!$this->getParameter('card')) {
            throw new InvalidRequestException('You must pass a "card" parameter.');
        }

        $this->getCard()->validate();

        // Two digit month.
        $expiryDateMonth = str_pad($this->getCard()->getExpiryMonth(), 2, 0, STR_PAD_LEFT);
        // Last two digits of the year only.
        $expiryDateYear = substr($this->getCard()->getExpiryYear(), strlen($this->getCard()->getExpiryYear()) - 2);

        return [
            'cardNumber' => $this->getCard()->getNumber(),
            'cardHolderName' => $this->getCard()->getName(),
            // 'cvn' => $this->getCard()->getCvv(), This isn't in the API docs...
            'expiry' => sprintf('%s/%s', $expiryDateMonth, $expiryDateYear),
        ];
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->getBaseEndpoint() . '/cardproxies';
    }

    public function getHttpMethod()
    {
        return 'POST';
    }
}
