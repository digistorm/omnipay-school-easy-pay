<?php

namespace Omnipay\SchoolEasyPay\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Response class for all SchoolEasyPay requests
 */
class PurchaseResponse extends AbstractResponse
{
    /** @var string Request ID */
    protected $requestId = null;
    /** @var string HTTP response code */
    protected $httpResponseCode = null;

    /**
     * Is the transaction successful?
     * @return boolean True if successful
     */
    public function isSuccessful()
    {
        // get response code
        $code = $this->getHttpResponseCode();

        if ($code === 200) {  // OK
            return true;
        }

        if ($code === 201 && $this->isApproved()) {   // Created
            return true;
        }

        if ($code === 202 && $this->isPending()) {   // Accepted
            return true;
        }

        return false;
    }

    /**
     * Is the transaction approved?
     * @return boolean True if approved
     */
    public function isApproved()
    {
        return $this->getStatus() === 'Successful';
    }

    /**
     * Is the transaction pending?
     * @return boolean True if pending
     */
    public function isPending()
    {
        return $this->getStatus() === 'Pending';
    }

    /**
     * Get Transaction reference
     * @return string Payway transaction reference
     */
    public function getTransactionReference()
    {
        return $this->getData('paymentReference');
    }

    public function getSettlementDate()
    {
        return $this->getData('settlementDate');
    }

    /**
     * Get status
     * @return array Returned status
     */
    public function getStatus()
    {
        return $this->getData('paymentStatus');
    }

    /**
     * Get response data, optionally by key
     * @param  string       $key Data array key
     * @return string|array      Response data item or all data if no key specified
     */
    public function getData($key = null)
    {
        if ($key) {
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }
        return $this->data;
    }

    /**
     * Get error data from response
     * @param  string       $key Optional data key
     * @return string|array      Response error item or all data if no key
     */
    public function getErrorData($key = null)
    {
        if ($this->isSuccessful()) {
            return null;
        }
        // get error data (array in data)
        $data = isset($this->getData('data')[0]) ? $this->getData('data')[0] : null;
        if ($key) {
            return isset($data[$key]) ? $data[$key] : null;
        }
        return $data;
    }

    /**
     * Get error message from the response
     * @return string|null Error message or null if successful
     */
    public function getMessage()
    {
        if ($this->getErrorMessage()) {
            return $this->getErrorMessage() . ' (' . $this->getErrorFieldName() . ')';
        }

        if ($this->isSuccessful()) {
            return ($this->getStatus()) ? ucfirst($this->getStatus()) : 'Successful';
        }
        // default to unsuccessful message
        return 'The transaction was unsuccessful.';
    }

    /**
     * Get code
     * @return string|null Error message or null if successful
     */
    public function getCode()
    {
        return join(' ', [
            $this->getResponseCode(),
            $this->getResponseText(),
            '(' . $this->getHttpResponseCode(),
            $this->getHttpResponseCodeText() . ')',
        ]);
    }

    /**
     * Get error message from the response
     * @return string|null Error message or null if successful
     */
    public function getErrorMessage()
    {
        return $this->getErrorData('message');
    }

    /**
     * Get field name in error from the response
     * @return string|null Error message or null if successful
     */
    public function getErrorFieldName()
    {
        return $this->getErrorData('fieldName');
    }

    /**
     * Get field value in error from the response
     * @return string|null Error message or null if successful
     */
    public function getErrorFieldValue()
    {
        return $this->getErrorData('fieldValue');
    }

    /**
     * Get Payway Response Code
     * @return string Returned response code
     */
    public function getResponseCode()
    {
        return $this->getData('responseCode');
    }

    /**
     * Get Payway Response Text
     * @return string Returned response Text
     */
    public function getResponseText()
    {
        return $this->getData('responseText');
    }

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set request id
     * @return AbstractRequest provides a fluent interface.
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Get HTTP Response Code
     * @return string
     */
    public function getHttpResponseCode()
    {
        return $this->httpResponseCode;
    }

    /**
     * Set HTTP Response Code
     * @parm string Response Code
     */
    public function setHttpResponseCode($value)
    {
        $this->httpResponseCode = $value;
    }

    /**
     * Get HTTP Response code text
     * @return string
     */
    public function getHttpResponseCodeText()
    {
        $code = $this->getHttpResponseCode();
        $statusTexts = \Symfony\Component\HttpFoundation\Response::$statusTexts;

        return (isset($statusTexts[$code])) ? $statusTexts[$code] : null;
    }
}
