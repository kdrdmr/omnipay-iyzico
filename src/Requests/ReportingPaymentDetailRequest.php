<?php

namespace Omnipay\Iyzico\Requests;

use Iyzipay\JsonBuilder;
use Iyzipay\Request;

class ReportingPaymentDetailRequest extends Request\ReportingPaymentDetailRequest
{
    private $paymentId;

    public function getPaymentId()
    {
        return $this->paymentId;
    }

    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function getJsonObject()
    {
        return JsonBuilder::fromJsonObject(parent::getJsonObject())
            ->add("paymentConversationId", $this->getPaymentConversationId())
            ->add("paymentId", $this->getPaymentId())
            ->getObject();
    }
}