<?php

namespace Omnipay\Iyzico\Messages;

use Exception;
use Omnipay\Common\Exception\InvalidResponseException;
use Iyzipay\Model\Payment;
use Iyzipay\Request\RetrievePaymentRequest;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Iyzico\IyzicoHttp;

class PurchaseInfoRequest extends AbstractRequest
{
    /**
     * @return RetrievePaymentRequest
     */
    public function getData(): RetrievePaymentRequest
    {
        $request = new RetrievePaymentRequest();
        $request->setLocale($this->getLocale());
        $request->setPaymentId($this->getPaymentId());

        $this->setRequestParams($this->transformIyzicoRequest($request));

        return $request;
    }

    /**
     * @param mixed $data
     * @return ResponseInterface|PurchaseInfoResponse
     * @throws InvalidResponseException
     */
    public function sendData($data): PurchaseInfoResponse
    {
        try {
            $options = $this->getOptions();
            Payment::setHttpClient(new IyzicoHttp($this->httpClient));
            $this->response = new PurchaseInfoResponse($this, Payment::retrieve($data, $options));
            /**
             * @var $client IyzicoHttp
             */
            $client = Payment::httpClient();
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

    public function getSensitiveData(): array
    {
        return [];
    }
}