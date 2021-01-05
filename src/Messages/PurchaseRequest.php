<?php

namespace Omnipay\Iyzico\Messages;

use Iyzipay\Model\Payment;
use Omnipay\Iyzico\IyzicoHttp;

class PurchaseRequest extends AbstractRequest
{
    use PurchaseRequestTrait;

    /**
     * @inheritDoc
     */
    public function sendData($data)
    {
        # make request
        $options = $this->getOptions();
        Payment::setHttpClient(new IyzicoHttp($this->httpClient));
        $this->response = new PurchaseResponse($this, Payment::create($data, $options));
        /**
         * @var $client IyzicoHttp
         */
        $client = Payment::httpClient();
        $this->setIyzicoUrl($client->getUrl());
        $requestParams = $this->getRequestParams();
        $this->response->setServiceRequestParams($requestParams);


        return $this->response;
    }
}