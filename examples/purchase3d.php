<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once (__DIR__ . "/Helper.php");

use Omnipay\Iyzico\IyzicoGateway;
use Omnipay\Iyzico\Messages\Purchase3dResponse;

$gateway = new IyzicoGateway();
$helper = new Helper();

try {
    $params = $helper->getPurchase3dParams();
    /** @var Purchase3dResponse $response */
    $response = $gateway->purchase($params)->send();

    $result = [
        'status' => $response->isSuccessful() ?: 0,
        'redirect' => $response->isRedirect() ?: 0,
        'redirectUrl' => $response->getRedirectUrl() ?: null,
        'redirectData' => $response->getRedirectData(),
        'htmlData' => $response->getThreeDHtmlContent(),
        'message' => $response->getMessage(),
        'transactionId' => $response->getTransactionReference(),
        'requestParams' => $response->getServiceRequestParams(),
        'response' => $response->getData()
    ];

    print("<pre>" . print_r($result, true) . "</pre>");
} catch (Exception $e) {
    throw new RuntimeException($e->getMessage());
}
