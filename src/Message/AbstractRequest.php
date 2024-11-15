<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Message;

use Omnipay\Common\Message\AbstractRequest as CommonAbstractRequest;
use Omnipay\Common\Message\ResponseInterface;

/**
 * @link https://api.schooleasypay.com.au/swagger/ui/index
 */
abstract class AbstractRequest extends CommonAbstractRequest
{
    abstract public function getEndpoint(): string;

    public function getBaseEndpoint(): string
    {
        return $this->getTestMode()
            ? 'https://apiuat.schooleasypay.com.au/v2'
            : 'https://api.schooleasypay.com.au/v2';
    }

    /**
     * Get API publishable key
     */
    public function getApiKey(): ?string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Set API publishable key
     */
    public function setApiKey(string $value): self
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getUsername(): ?string
    {
        return $this->getParameter('username');
    }

    public function setUsername(string $value): self
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword(): ?string
    {
        return $this->getParameter('password');
    }

    public function setPassword(string $value): self
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Get Idempotency Key
     */
    public function getIdempotencyKey(): ?string
    {
        return $this->getParameter('idempotencyKey') ?: uniqid();
    }

    /**
     * Set Idempotency Key
     */
    public function setIdempotencyKey(string $value): self
    {
        return $this->setParameter('idempotencyKey', $value);
    }

    /**
     * Get HTTP method
     */
    public function getHttpMethod(): string
    {
        return 'GET';
    }

    /**
     * Get request headers
     */
    public function getRequestHeaders(): array
    {
        // common headers
        $headers = ['Accept' => 'application/json'];

        // set content type
        if ($this->getHttpMethod() !== 'GET') {
            $headers['Content-Type'] = 'application/json';
        }

        // prevent duplicate POSTs
        if ($this->getHttpMethod() === 'POST') {
            $headers['Idempotency-Key'] = $this->getIdempotencyKey();
        }

        return $headers;
    }

    /**
     * Send data request
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
            '1.2' // Enforce TLS v1.2
        );

        $content = $httpResponse->getBody()->getContents();

        $response = new Response($this, json_decode($content, true));

        // save additional info
        $response->setHttpResponseCode($httpResponse->getStatusCode());

        $this->response = $response;

        return $this->response;
    }
}
