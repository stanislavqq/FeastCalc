<?php

namespace App\Service\Fiscal;

use chillerlan\QRCode\QRCode;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;

class FiscalService
{
    public function __construct(
        private FiscalAPI $fiscalAPI,
        private CacheInterface $cache,
    )
    {
    }

    public function loadCheckData(string $filePath) : Check
    {
        $qrData = $this->parseQR($filePath);

        $responseData = $this->cache->get($qrData, function (CacheItem $cacheItem) use ($qrData) {
            $cacheItem->expiresAfter(3600);
            return $this->fiscalAPI->loadFiscalData($qrData);
        });


        $check = Check::parseArray($responseData);
        return $check;
    }
    public function parseQR(string $filePath) : string
    {

        try {
            $result = new QRCode()->readFromFile($filePath);
        } catch (\Throwable $e) {
            throw $e;
        }

        return $result->data;
    }


}



