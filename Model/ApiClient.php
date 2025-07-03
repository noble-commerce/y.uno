<?php
declare(strict_types=1);

namespace NobleCommerce\Yuno\Model;

use Magento\Framework\HTTP\Client\Curl;
use NobleCommerce\Yuno\Model\Config\ConfigProvider;
use Psr\Log\LoggerInterface;

class ApiClient
{
    public function __construct(
        private readonly Curl $curl,
        private readonly ConfigProvider $configProvider,
        private readonly LoggerInterface $logger
    ) {}

    public function createPayment(array $payload, string $storeCode = null): array
    {
        $url = $this->getBaseUrl($storeCode) . 'checkout-sessions'; // endpoint de exemplo
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getPrivateKey($storeCode),
            'X-Idempotency-Key' => 'magento_' . uniqid(),
        ];

        $this->curl->setHeaders($headers);
        $this->curl->post($url, json_encode($payload));

        $status = $this->curl->getStatus();
        $response = json_decode($this->curl->getBody(), true);

        if ($status >= 200 && $status < 300) {
            return $response;
        }

        $this->logger->error('[Yuno] API Error', ['status' => $status, 'response' => $response]);
        throw new \RuntimeException('Erro ao comunicar com a Yuno');
    }

    private function getBaseUrl(?string $storeCode): string
    {
        return $this->configProvider->getEnvironment($storeCode) === 'production'
            ? rtrim((string) $this->configProvider->getProductionBaseUrl($storeCode), '/') . '/'
            : rtrim((string) $this->configProvider->getSandboxBaseUrl($storeCode), '/') . '/';
    }

    private function getPrivateKey(?string $storeCode): string
    {
        return $this->configProvider->getEnvironment($storeCode) === 'production'
            ? (string) $this->configProvider->getProductionPrivateSecretKey($storeCode)
            : (string) $this->configProvider->getSandboxPrivateSecretKey($storeCode);
    }
}
