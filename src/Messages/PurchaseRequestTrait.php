<?php

namespace Omnipay\Iyzico\Messages;

use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Request\CreatePaymentRequest;
use Omnipay\Common\CreditCard;
use Omnipay\Common\ItemBag;
use Omnipay\Iyzico\Customer;
use Omnipay\Iyzico\IyzicoItem;
use Omnipay\Iyzico\IyzicoItemBag;

trait PurchaseRequestTrait
{

    public function setItems($items)
    {
        if ($items && !$items instanceof ItemBag) {
            $items = new IyzicoItemBag($items);
        }

        return $this->setParameter('items', $items);
    }

    /**
     * @inheritDoc
     */
    public function getData(): CreatePaymentRequest
    {
        /*  @var $card CreditCard */
        $card = $this->getCard();
        /** @var  $customer Customer */
        $customer = $this->getCustomer();
        $request = new CreatePaymentRequest();
        $request->setLocale($this->getLocale());
        $request->setConversationId($this->getConversationId());
        $request->setPrice($this->getAmount());
        $request->setPaidPrice($this->getPaidPrice());
        $request->setCurrency($this->getCurrency());
        $request->setInstallment($this->getInstallment());
        ($this->getConnectorName() !== null) ? $request->setConnectorName($this->getConnectorName()) : null; // ConnectorName is optional
        ($this->getPosOrderId() !== null) ? $request->setPosOrderId($this->getPosOrderId()) : null; // PosOrderId is optional
        ($this->getBasketId() !== null) ? $request->setBasketId($this->getBasketId()) : null; // BasketId is optional
        ($this->getPaymentChannel() !== null) ? $request->setPaymentChannel($this->getPaymentChannel()) : null; // PaymentChannel is optional
        ($this->getPaymentGroup() !== null) ? $request->setPaymentGroup($this->getPaymentGroup()) : null; // PaymentGroup is optional
        (!empty($this->getCallBackUrl())) ? $request->setCallbackUrl($this->getCallBackUrl()) : null;

        $paymentCard = new PaymentCard();
        if($this->getCardUserKey() !== null) {
            $paymentCard->setCardUserKey($this->getCardUserKey()); // RegisterCard is optional
        }
        if($this->getCardToken() !== null){
            $paymentCard->setCardToken($this->getCardToken()); // RegisterCard is optional
        } else{
            $paymentCard->setCardHolderName($card->getName());
            $paymentCard->setCardNumber($card->getNumber());
            $paymentCard->setExpireMonth($card->getExpiryMonth());
            $paymentCard->setExpireYear($card->getExpiryYear());
            $paymentCard->setCvc($card->getCvv());
            ($this->getRegisterCard() !== null) ? $paymentCard->setRegisterCard($this->getRegisterCard()) : null; // RegisterCard is optional
        }
        $request->setPaymentCard($paymentCard);

        $buyer = new Buyer();
        $buyer->setId($customer->getClientId());
        $buyer->setName($customer->getClientName());
        $buyer->setSurname($customer->getClientSurName());
        ($customer->getClientPhone() !== null) ? $buyer->setGsmNumber($customer->getClientPhone()) : null; // GsmNumber is optional
        $buyer->setEmail($customer->getClientEmail());
        $buyer->setIdentityNumber($customer->getIdentityNumber());
        ($customer->getLastLoginDate() !== null) ? $buyer->setLastLoginDate($customer->getLastLoginDate()) : null; // LastLoginDate is optional
        ($customer->getRegistrationDate() !== null) ? $buyer->setRegistrationDate($customer->getRegistrationDate()) : null; // RegistrationDate is optional
        $buyer->setRegistrationAddress($customer->getClientAddress()); // If 'registrationAddress' exists, else CreditCard -> Address1
        $buyer->setIp($this->getClientIp());
        $buyer->setCity($customer->getClientCity());
        $buyer->setCountry($customer->getClientCountry());
        ($customer->getPostcode() !== null) ? $buyer->setZipCode($customer->getPostcode()) : null; // PostCode is optional
        $request->setBuyer($buyer);

        $shippingAddress = new Address();
        $shippingAddress->setContactName($card->getShippingName());
        $shippingAddress->setCity($card->getShippingCity());
        $shippingAddress->setCountry($card->getShippingCountry());
        $shippingAddress->setAddress($card->getShippingAddress1());
        ($card->getShippingPostcode() !== null) ? $shippingAddress->setZipCode($card->getShippingPostcode()) : null; // ShippingPostCode is optional
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new Address();
        $billingAddress->setContactName($card->getBillingName());
        $billingAddress->setCity($card->getBillingCity());
        $billingAddress->setCountry($card->getBillingCountry());
        $billingAddress->setAddress($card->getBillingAddress1());
        ($card->getBillingPostcode() !== null) ? $billingAddress->setZipCode($card->getBillingPostcode()) : null; // BillingPostcode is optional
        $request->setBillingAddress($billingAddress);

        $basketItems = $this->getBasketItems();

        $request->setBasketItems($basketItems);

        $this->setRequestParams($this->transformIyzicoRequest($request));

        return $request;
    }

    public function getBasketItems(): array
    {
        $basketItems = [];
        /* @var IyzicoItemBag $itemBag */
        $items = $this->getItems();
        /* @var IyzicoItem $item */
        foreach ($items as $item) {
            $basketItem = new BasketItem();
            $basketItem->setId($item->getId());
            $basketItem->setName($item->getName());
            $basketItem->setCategory1($item->getCategory1());
            ($item->getCategory2() !== null) ? $basketItem->setCategory2($item->getCategory2()) : null; // Category2 is optional
            $basketItem->setItemType($item->getItemType());
            $basketItem->setPrice($item->getPrice());
            $basketItems[] = $basketItem;
        }
        return $basketItems;
    }

    public function setPaidPrice($value): void
    {
        $this->setParameter('paidPrice', $value);
    }

    public function setInstallment($value): void
    {
        $this->setParameter('installment', $value);
    }

    public function setBasketId($value): void
    {
        $this->setParameter('basketId', $value);
    }

    public function setPaymentChannel($value): void
    {
        $this->setParameter('paymentChannel', $value);
    }

    public function setPaymentGroup($value): void
    {
        $this->setParameter('paymentGroup', $value);
    }

    public function setRegisterCard($value): void
    {
        $this->setParameter('registerCard', $value);
    }

    public function getCallBackUrl(): ?string
    {
        return '';
    }

    public function setConversationId($value): void
    {
        $this->setParameter('conversationId', $value);
    }

    public function getSensitiveData(): array
    {
        return ['cardHolderName', 'cardNumber', 'expireYear', 'expireMonth', 'cvc', 'registerCard', 'cardAlias'];
    }

    private function getConversationId(): string
    {
        return $this->getParameter('conversationId') ?? '';
    }

    private function getPaidPrice()
    {
        return $this->getParameter('paidPrice') ?? $this->getAmount();
    }

    private function getInstallment(): int
    {
        return $this->getParameter('installment') ?? 1;
    }

    private function getBasketId()
    {
        return $this->getParameter('basketId');
    }

    public function getConnectorName()
    {
        return $this->getParameter('connectorName');
    }

    public function getPosOrderId()
    {
        return $this->getParameter('posOrderId');
    }

    private function getPaymentChannel()
    {
        return $this->getParameter('paymentChannel');
    }

    private function getPaymentGroup()
    {
        return $this->getParameter('paymentGroup');
    }

    private function getRegisterCard()
    {
        return $this->getParameter('registerCard');
    }

    private function getCardUserKey()
    {
        return $this->getParameter('cardUserKey');
    }

    private function getCardToken()
    {
        return $this->getParameter('cardToken');
    }

    public function setCardUserKey($cardUserKey)
    {
        return $this->setParameter('cardUserKey', $cardUserKey);
    }

    public function setCardToken($cardToken)
    {
        return $this->setParameter('cardToken', $cardToken);
    }

    /**
     * Sets the card reference.
     *
     * @param  $value
     * @return $this
     */
    public function setPosOrderId($value)
    {
        return $this->setParameter('posOrderId', $value);
    }

    /**
     * Sets the card reference.
     *
     * @param  $value
     * @return $this
     */
    public function setConnectorName($value)
    {
        return $this->setParameter('connectorName', $value);
    }

}