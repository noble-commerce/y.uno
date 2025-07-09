<?php
/*
 * Copyright © 2025 NobleCommerce. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NobleCommerce\Yuno\Gateway\Http;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Client
 *
 * This class is responsible for making HTTP requests to the Yuno gateway.
 * It uses the Curl client to send requests and handle responses.
 */
class Client implements ClientInterface
{
    /**
     * Client constructor.
     *
     * @param Curl $curl
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly Curl   $curl,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Places a request to the Yuno API.
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws LocalizedException
     */
    public function placeRequest(TransferInterface $transferObject): array
    {
        $uri = $transferObject->getUri();
        $method = strtoupper($transferObject->getMethod());
        $headers = $transferObject->getHeaders();
        $body = $transferObject->getBody();

        try {
            $this->curl->setHeaders($headers);
            $this->curl->addHeader("Content-Type", "application/json");

            switch ($method) {
                case 'POST':
                    $this->curl->post($uri, json_encode($body));
                    break;
                case 'PUT':
                    $this->curl->put($uri, json_encode($body));
                    break;
                case 'GET':
                    $this->curl->get($uri);
                    break;
                default:
                    throw new LocalizedException(__("Unsupported method: %1", $method));
            }

            $response = $this->curl->getBody();
            return json_decode($response, true) ?? [];
        } catch (\Throwable $e) {
            $this->logger->error('[Yuno Gateway] HTTP Error: ' . $e->getMessage());
            throw new LocalizedException(__('Payment communication error.'));
        }
    }
}
