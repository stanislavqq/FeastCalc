<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Service\Fiscal\Check;

class CheckTest extends TestCase
{
    public function testParseJson()
    {
        $json = <<<JSON
{
    "messageFiscalSign": 9297262765723324000,
    "code": 3,
    "fiscalDocumentFormatVer": 4,
    "fiscalDriveNumber": "7380440801990956",
    "kktRegId": "0006269989035822    ",
    "userInn": "7840098280  ",
    "fiscalDocumentNumber": 9630,
    "dateTime": "2025-12-19T23:47:00",
    "fiscalSign": 1198294572,
    "shiftNumber": 163,
    "requestNumber": 58,
    "operationType": 1,
    "totalSum": 6179800,
    "items": [
        {
            "itemsQuantityMeasure": 0,
            "name": "Мохито б/а NEW",
            "price": 64900,
            "quantity": 1,
            "sum": 64900,
            "nds": 6,
            "paymentType": 4,
            "productType": 1
        },
        {
            "itemsQuantityMeasure": 0,
            "name": "Мохито б/а NEW",
            "price": 64900,
            "quantity": 1,
            "sum": 64900,
            "nds": 6,
            "paymentType": 4,
            "productType": 1
        },
        {
            "itemsQuantityMeasure": 41,
            "itemsIndustryDetails": [
                {
                    "idFoiv": "048",
                    "foundationDocDateTime": "29.12.2022",
                    "foundationDocNumber": "597",
                    "industryPropValue": "mode=horeca"
                }
            ],
            "productCodeNew": {
                "itf14": {
                    "rawProductCode": "04627083415024",
                    "productIdType": 5,
                    "gtin": "04627083415024",
                    "sernum": ""
                }
            },
            "labelCodeProcesMode": 0,
            "name": "Вайсмюллер Хефевайсбир 0.5",
            "price": 151800,
            "quantity": 0.5,
            "sum": 75900,
            "nds": 6,
            "paymentType": 4,
            "productType": 33,
            "checkingProdInformationResult": 0
        }
    ]
}
JSON;

        $check = Check::parseJson($json);

        self::assertEquals("2025-12-19 23:47:00", $check->getDateTime()->format('Y-m-d H:i:s'));
        self::assertEquals(6179800, $check->getTotalSum());
        self::assertEquals("Мохито б/а NEW", $check->get(0)?->getName());
        self::assertEquals(null, $check->get(-1)?->getName());
        self::assertEquals(3, $check->getItemsCount());
    }
}
