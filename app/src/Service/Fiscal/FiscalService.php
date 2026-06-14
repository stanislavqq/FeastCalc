<?php

namespace App\Service\Fiscal;

use chillerlan\QRCode\QRCode;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Cache\CacheInterface;

class FiscalService
{
    public function __construct(
        private FiscalAPI $fiscalAPI,
        private CacheInterface $cache,
    )
    {
    }

    public function loadCheckData(UploadedFile $file) : Check
    {
        $qrData = $this->parseQR($file);

        $responseData = $this->cache->get($qrData, function (CacheItem $cacheItem) use ($qrData) {
            $cacheItem->expiresAfter(3600);
            return $this->fiscalAPI->loadFiscalData($qrData);
        });


        $check = Check::parseArray($responseData);
        return $check;
    }
    public function parseQR(UploadedFile $file) : string
    {

        try {
            $blobData = file_get_contents($file->getPathname());
            $result = new QRCode()->readFromBlob($blobData);
        } catch (\Throwable $e) {
            throw $e;
        }

        return $result->data;
    }


}



