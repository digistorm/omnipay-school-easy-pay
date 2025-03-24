<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Response class for all SchoolEasyPay requests
 */
class Response extends AbstractResponse
{
    protected string $requestId;

    protected string $httpResponseCode;

    /**
     * Is the transaction successful?
     */
    public function isSuccessful(): bool
    {
        // get response code
        $code = $this->getHttpResponseCode();

        if ($code === '200' || $code === '201') {
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
        return $this->getStatus() === 'pending';
    }

    public function getSettlementDate(): ?string
    {
        return $this->getDataItem('settlementDate');
    }

    /**
     * Get Transaction ID
     */
    public function getTransactionId(): ?string
    {
        return $this->getDataItem('transactionId');
    }

    /**
     * Get Transaction reference
     */
    public function getTransactionReference(): ?string
    {
        return $this->getDataItem('receiptNumber');
    }

    /**
     * Get Customer Number
     */
    public function getCustomerNumber(): ?string
    {
        return $this->getDataItem('customerNumber');
    }

    /**
     * Get Contact details
     * @todo Investigate whether to remove this - pretty sure it's a carryover from the Westpac Payway code
     */
    public function getContact(): array
    {
        return $this->getData()['contact'] ?? [];
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
        return $this->data ?? [];
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
    public function getMessage(): ?string
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
        return $this->getDataItem('message');
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
    public function getHttpResponseCode(): string
    {
        return $this->httpResponseCode;
    }

    /**
     * Set HTTP Response Code
     */
    public function setHttpResponseCode(string|int $value): void
    {
        $this->httpResponseCode = (string) $value;
    }

    /**
     * Get HTTP Response code text
     */
    public function getHttpResponseCodeText(): ?string
    {
        $code = $this->getHttpResponseCode();
        $statusTexts = SymfonyResponse::$statusTexts;

        return $statusTexts[$code] ?? null;
    }

    /**
     * Get transaction type
     */
    public function getTransactionType(): ?string
    {
        return $this->getDataItem('transactionType');
    }

    /**
     * Get payment method
     */
    public function getPaymentMethod(): string
    {
        return $this->getDataItem('paymentMethod');
    }

    /**
     * Get credit card information
     */
    public function getCreditCard(): string
    {
        return $this->getDataItem('creditCard');
    }

    /**
     * Get bank account information
     */
    public function getBankAccount(): string
    {
        return $this->getDataItem('bankAccount');
    }
}
