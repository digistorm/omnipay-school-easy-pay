<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Message;

use InvalidArgumentException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response class for all SchoolEasyPay requests
 */
class PurchaseResponse extends AbstractResponse
{
    protected string $requestId;

    protected string $httpResponseCode;

    public function __construct(RequestInterface $request, mixed $data)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('Request data must be an array');
        }
        parent::__construct($request, $data);
    }

    /**
     * Is the transaction successful?
     */
    public function isSuccessful(): bool
    {
        // get response code
        $code = $this->getHttpResponseCode();

        if ($code === '200' || ($code === '201' && $this->isApproved())) {
            return true;
        }

        // Accepted
        return $code === '202' && $this->isPending();
    }

    /**
     * Is the transaction approved?
     */
    public function isApproved(): bool
    {
        return $this->getStatus() === 'Successful';
    }

    /**
     * Is the transaction pending?
     */
    public function isPending(): bool
    {
        return $this->getStatus() === 'Pending';
    }

    /**
     * Get Transaction reference
     */
    public function getTransactionReference(): string
    {
        return $this->getDataItem('paymentReference');
    }

    public function getSettlementDate(): string
    {
        return $this->getDataItem('settlementDate');
    }

    /**
     * Get status
     */
    public function getStatus(): string
    {
        return $this->getDataItem('paymentStatus');
    }

    public function getDataItem(string $key): ?string
    {
        $data = $this->getData();

        return $data[$key] ?? null;
    }

    /**
     * Get all response data
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getErrorDataItem(string $key): ?string
    {
        $data = $this->getErrorData();

        return $data[$key] ?? null;
    }

    /**
     * Get error data from response
     */
    public function getErrorData(): ?array
    {
        if ($this->isSuccessful()) {
            return null;
        }

        $data = $this->getData();

        return $data['data'][0] ?? [];
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
            return ($this->getStatus() !== '' && $this->getStatus() !== '0') ? ucfirst($this->getStatus()) : 'Successful';
        }

        // default to unsuccessful message
        return 'The transaction was unsuccessful.';
    }

    /**
     * Get code
     */
    public function getCode(): ?string
    {
        return implode(' ', [
            $this->getResponseMessage(),
            '(' . $this->getHttpResponseCode(),
            $this->getHttpResponseCodeText() . ')',
        ]);
    }

    /**
     * Get error message from the response
     */
    public function getErrorMessage(): ?string
    {
        return $this->getErrorDataItem('message');
    }

    /**
     * Get field name in error from the response
     */
    public function getErrorFieldName(): ?string
    {
        return $this->getErrorDataItem('fieldName');
    }

    /**
     * Get field value in error from the response
     */
    public function getErrorFieldValue(): ?string
    {
        return $this->getErrorDataItem('fieldValue');
    }

    public function getResponseMessage(): ?string
    {
        return $this->getDataItem('responseMessage');
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    /**
     * Set request id
     */
    public function setRequestId(string $requestId): void
    {
        $this->requestId = $requestId;
    }

    /**
     * Get HTTP Response Code
     */
    public function getHttpResponseCode(): ?string
    {
        return $this->httpResponseCode;
    }

    /**
     * Set HTTP Response Code
     */
    public function setHttpResponseCode(string $value): void
    {
        $this->httpResponseCode = $value;
    }

    /**
     * Get HTTP Response code text
     */
    public function getHttpResponseCodeText(): ?string
    {
        $code = $this->getHttpResponseCode();
        $statusTexts = Response::$statusTexts;

        return $statusTexts[$code] ?? null;
    }
}
