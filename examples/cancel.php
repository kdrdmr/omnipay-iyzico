<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once (__DIR__ . "/Helper.php");

use Omnipay\Iyzico\IyzicoGateway;


$gateway = new IyzicoGateway();
$helper = new Helper();

try {
    $params = $helper->getCancelPurchaseParams();
    $response = $gateway->void($params)->send();

    $result = [
        'status' => $response->isSuccessful() ?: 0,
        'cancel' => $response->isCancelled() ?: 0,
        'message' => $response->getMessage(),
        'transactionId' => $response->getTransactionReference(),
        'requestParams' => $response->getServiceRequestParams(),
        'response' => $response->getData()
    ];

    print("<pre>" . print_r($result, true) . "</pre>");
} catch (Exception $e) {
    throw new RuntimeException($e->getMessage());
}