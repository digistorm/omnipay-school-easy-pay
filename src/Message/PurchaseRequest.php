<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * @link https://www.payway.com.au/rest-docs/index.html#process-a-payment
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @throws InvalidRequestException
     */
    public function getData(): array
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

        $card = $this->getCard();
        $data['customerName'] = $card->getName();

        $data['paymentAmount'] = $this->getAmount();

        if ($this->getMerchantUniquePaymentId()) {
            $data['merchantUniquePaymentId'] = $this->getMerchantUniquePaymentId();
        }

        return $data;
    }

    public function getCardProxy(): ?string
    {
        return $this->getParameter('cardProxy');
    }

    public function setCardProxy(string $value): self
    {
        return $this->setParameter('cardProxy', $value);
    }

    public function getCustomerReference(): ?string
    {
        return $this->getParameter('customerReference');
    }

    public function setCustomerReference(string $value): self
    {
        return $this->setParameter('customerReference', $value);
    }

    public function getMerchantUniquePaymentId(): ?string
    {
        return $this->getParameter('merchantUniquePaymentId');
    }

    public function setMerchantUniquePaymentId(string $value): self
    {
        return $this->setParameter('merchantUniquePaymentId', $value);
    }

    public function getEndpoint(): string
    {
        return $this->getBaseEndpoint() . '/payments';
    }

    public function getHttpMethod(): string
    {
        return 'POST';
    }

    public function getUseSecretKey(): bool
    {
        return true;
    }

    protected function createResponse(mixed $data): PurchaseResponse
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
