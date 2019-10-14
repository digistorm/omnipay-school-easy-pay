<?php

namespace Omnipay\SchoolEasyPay\Message;

/**
 * @link https://www.payway.com.au/rest-docs/index.html
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    abstract public function getEndpoint();

    public function getBaseEndpoint()
    {
        return $this->getTestMode()
            ? 'https://apiuat.schooleasypay.com.au/v2'
            : 'https://api.schooleasypay.com.au/v2';
    }

    /**
     * Get API publishable key
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Set API publishable key
     * @param  string $value API publishable key
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getCustomerNumber()
    {
        return $this->getParameter('customerNumber');
    }

    public function setCustomerNumber($value)
    {
        return $this->setParameter('customerNumber', $value);
    }

    public function getTransactionType()
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType($value)
    {
        return $this->setParameter('transactionType', $value);
    }

    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }

    public function getPrincipalAmount()
    {
        return $this->getParameter('principalAmount');
    }

    public function setPrincipalAmount($value)
    {
        return $this->setParameter('principalAmount', $value);
    }

    public function getCurrency()
    {
        // PayWay expects lowercase currency values
        return ($this->getParameter('currency'))
            ? strtolower($this->getParameter('currency'))
            : null;
    }

    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }

    public function getOrderNumber()
    {
        return $this->getParameter('orderNumber');
    }

    public function setOrderNumber($value)
    {
        return $this->setParameter('orderNumber', $value);
    }

    public function getCustomerIpAddress()
    {
        return $this->getParameter('customerIpAddress');
    }

    public function setCustomerIpAddress($value)
    {
        return $this->setParameter('customerIpAddress', $value);
    }

    /**
     * Get Idempotency Key
     * @return string Idempotency Key
     */
    public function getIdempotencyKey()
    {
        return $this->getParameter('idempotencyKey') ?: uniqid();
    }

    /**
     * Set Idempotency Key
     * @param  string $value Idempotency Key
     */
    public function setIdempotencyKey($value)
    {
        return $this->setParameter('idempotencyKey', $value);
    }

    /**
     * Get HTTP method
     * @return string HTTP method (GET, PUT, etc)
     */
    public function getHttpMethod()
    {
        return 'GET';
    }

    /**
     * Get request headers
     * @return array Request headers
     */
    public function getRequestHeaders()
    {
        // common headers
        $headers = array(
            'Accept' => 'application/json',
        );

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
     *
     * @param $data
     *
     * @return \Omnipay\Common\Message\ResponseInterface|\Omnipay\SchoolEasyPay\Message\Response
     */
    public function sendData($data)
    {
        $headers = $this->getRequestHeaders();
        $headers['Api-Key'] = $this->getApiKey();
        $headers['Authorization'] = 'Basic ' . base64_encode(sprintf('%s:%s', $this->getUsername(), $this->getPassword()));

        $body = $data ? json_encode($data) : null;

        $response = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $headers,
            $body,
            '1.2' // Enforce TLS v1.2
        );

        $content = $response->getBody()->getContents();

        $this->response = new Response($this, json_decode($content, true));

        // save additional info
        $this->response->setHttpResponseCode($response->getStatusCode());
        $this->response->setTransactionType($this->getTransactionType());

        return $this->response;
    }
}
