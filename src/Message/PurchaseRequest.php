<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;

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

    /**
     * Override sendData to ensure a PurchaseResponse is used, which correctly
     * checks paymentStatus when determining success — SchoolEasyPay returns
     * HTTP 200 even for failed/declined transactions.
     */
    public function sendData(mixed $data): ResponseInterface
    {
        $headers = $this->getRequestHeaders();
        $headers['Api-Key'] = $this->getApiKey();
        $headers['Authorization'] = 'Basic ' . base64_encode(sprintf('%s:%s', $this->getUsername(), $this->getPassword()));

        $body = $data ? json_encode($data) : null;

        $httpResponse = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $headers,
            $body ?: null,
        );

        $content = (string) $httpResponse->getBody();

        $response = $this->createResponse(json_decode($content, true));
        $response->setHttpResponseCode((string) $httpResponse->getStatusCode());

        $this->response = $response;

        return $this->response;
    }

    protected function createResponse(mixed $data): PurchaseResponse
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
