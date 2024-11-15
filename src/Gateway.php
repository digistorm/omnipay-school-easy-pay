<?php

declare(strict_types=1);

namespace Omnipay\SchoolEasyPay;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\SchoolEasyPay\Message\CreateSingleUseCardTokenRequest;
use Omnipay\SchoolEasyPay\Message\PurchaseRequest;

/**
 * @method RequestInterface authorize(array $options = []) (Optional method)
 * Authorize an amount on the customers card
 * @method RequestInterface completeAuthorize(array $options = []) (Optional method)
 * Handle return from off-site gateways after authorization
 * @method RequestInterface capture(array $options = []) (Optional method)
 * Capture an amount you have previously authorized
 * @method RequestInterface completePurchase(array $options = []) (Optional method)
 * Handle return from off-site gateways after purchase
 * @method RequestInterface refund(array $options = []) (Optional method)
 * Refund an already processed transaction
 * @method RequestInterface void(array $options = []) (Optional method)
 * Generally can only be called up to 24 hours after submitting a transaction
 * @method RequestInterface createCard(array $options = []) (Optional method)
 * The returned response object includes a cardReference, which can be used for future transactions
 * @method RequestInterface updateCard(array $options = []) (Optional method)
 * Update a stored card
 * @method RequestInterface deleteCard(array $options = []) (Optional method)
 * Delete a stored card
 */
class Gateway extends AbstractGateway
{
    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName(): string
    {
        return 'School EasyPay';
    }

    /**
     * Get gateway short name
     *
     * This name can be used with GatewayFactory as an alias of the gateway class,
     * to create new instances of this gateway.
     */
    public function getShortName(): string
    {
        return 'School EasyPay';
    }

    public function getDefaultParameters(): array
    {
        return [
            'apiKey' => '',
            'username' => '',
            'password' => '',
        ];
    }

    /**
     * Get API key
     */
    public function getApiKey(): string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Set API key
     */
    public function setApiKey(string $value): self
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getUsername(): string
    {
        return $this->getParameter('username');
    }

    public function setUsername(string $value): self
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword(): string
    {
        return $this->getParameter('password');
    }

    public function setPassword(string $value): self
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Purchase request
     */
    public function purchase(array $options = []): AbstractRequest
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    /**
     * Create singleUseTokenId with a CreditCard
     */
    public function createSingleUseCardToken(array $options = []): AbstractRequest
    {
        return $this->createRequest(CreateSingleUseCardTokenRequest::class, $options);
    }
}
