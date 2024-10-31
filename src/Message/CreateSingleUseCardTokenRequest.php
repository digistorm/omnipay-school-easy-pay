<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * @link https://www.payway.com.au/docs/rest.html#tokenise-a-credit-card
 */
class CreateSingleUseCardTokenRequest extends AbstractRequest
{
    /**
     * @throws InvalidRequestException
     * @throws InvalidCreditCardException
     */
    public function getData(): array
    {
        if (!$this->getParameter('card')) {
            throw new InvalidRequestException('You must pass a "card" parameter.');
        }

        $this->getCard()->validate();

        $expiryDateMonthAsString = (string)$this->getCard()->getExpiryMonth();
        $expiryDateYearAsString = (string)$this->getCard()->getExpiryYear();
        // Two-digit month.
        $expiryDateMonth = str_pad($expiryDateMonthAsString, 2, '0', STR_PAD_LEFT);
        // Last two digits of the year only.
        $expiryDateYear = substr($expiryDateYearAsString, strlen($expiryDateYearAsString) - 2);

        return [
            'cardNumber' => $this->getCard()->getNumber(),
            'cardHolderName' => $this->getCard()->getName(),
            'expiry' => sprintf('%s/%s', $expiryDateMonth, $expiryDateYear),
        ];
    }

    public function getEndpoint(): string
    {
        return $this->getBaseEndpoint() . '/cardproxies';
    }

    public function getHttpMethod(): string
    {
        return 'POST';
    }
}
