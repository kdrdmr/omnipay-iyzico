<?php

namespace Omnipay\Iyzico\Messages;

use Exception;
use Iyzipay\Model\ReportingPaymentDetail;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Iyzico\IyzicoHttp;

class ReportingPaymentDetailRequest extends AbstractRequest
{

    /**
     * Prepare payment refund data.
     *
     * @return \Omnipay\Iyzico\Requests\ReportingPaymentDetailRequest
     */
    public function getData(): \Omnipay\Iyzico\Requests\ReportingPaymentDetailRequest
    {
        $request = new \Omnipay\Iyzico\Requests\ReportingPaymentDetailRequest();
        //$request->setLocale($this->getLocale());
        $request->setPaymentId($this->getPaymentId());
        $request->setPaymentConversationId($this->getPaymentConversationId());
        //$request->setConversationId($this->getPaymentConversationId());

        $this->setRequestParams($this->transformIyzicoRequest($request));

        return $request;
    }

    /**
     * Send request for payment refund process.
     *
     * @param mixed $data
     * @return ReportingPaymentDetailResponse
     * @throws InvalidResponseException
     */
    public function sendData($data): ReportingPaymentDetailResponse
    {
        try {
            $options = $this->getOptions();
            ReportingPaymentDetail::setHttpClient(new IyzicoHttp($this->httpClient));
            $this->response = new ReportingPaymentDetailResponse($this, ReportingPaymentDetail::create($data, $options));
            /**
             * @var $client IyzicoHttp
             */
            $client = ReportingPaymentDetail::httpClient();
            $this->setIyzicoUrl($client->getUrl());
            $requestParams = $this->getRequestParams();
            $this->response->setServiceRequestParams($requestParams);

            return $this->response;
        } catch (Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * Set payment transaction id for item.
     *
     * @param string $paymentTransactionId
     */
    public function setPaymentId(string $paymentTransactionId): void
    {
        $this->setParameter('paymentId', $paymentTransactionId);
    }

    /**
     * Get payment transaction id for item.
     *
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->getParameter('paymentId');
    }

    /**
     * Set payment transaction Conversation id for item.
     *
     * @param string $paymentConversationId
     */
    public function setPaymentConversationId( string $paymentConversationId): void
    {
        $this->setParameter('paymentConversationId', $paymentConversationId);
    }

    /**
     * Get payment transaction Conversation id for item.
     *
     * @return string
     */
    public function getPaymentConversationId(): string
    {
        return $this->getParameter('paymentConversationId');
    }

    public function getSensitiveData(): array
    {
        return [];
    }
}