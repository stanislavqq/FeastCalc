<?php

namespace App\Tests;

use App\Service\Fiscal\CheckItem;
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
        },
        {
            "nds": 6,
            "sum": 104000,
            "name": "Вайценфельд Вайс 0,5",
            "price": 104000,
            "quantity": 1,
            "paymentType": 4,
            "productType": 33,
            "productCodeNew": {
                "itf14": {
                    "gtin": "04640000124939",
                    "sernum": "",
                    "productIdType": 5,
                    "rawProductCode": "04640000124939"
                }
            },
            "labelCodeProcesMode": 0,
            "itemsIndustryDetails": [
                {
                    "idFoiv": "048",
                    "industryPropValue": "mode=horeca",
                    "foundationDocNumber": "597",
                    "foundationDocDateTime": "29.12.2022"
                }
            ],
            "itemsQuantityMeasure": 41,
            "checkingProdInformationResult": 0
        },
        {
            "sum": 104000,
            "name": "test pos 0,5",
            "price": 104000,
            "quantity": 1,
            "itemsQuantityMeasure": 41
        },
        {
            "sum": 104000,
            "name": "test pos 0,5",
            "price": 104000,
            "quantity": 1,
            "itemsQuantityMeasure": 41
        }
    ]
}
JSON;

        $check = Check::parseArray(json_decode($json, true));

        self::assertEquals("2025-12-19 23:47:00", $check->getDateTime()->format('Y-m-d H:i:s'));
        self::assertEquals(6179800, $check->getTotalSum());
        self::assertEquals("Мохито б/а NEW", $check->get(0)?->getName());
        self::assertEquals(null, $check->get(-1)?->getName());
        self::assertEquals(4, $check->getItemsCount());


        self::assertEquals("Вайсмюллер Хефевайсбир 0.5", $check->get(1)->getName());
//        dd($check->get(1)->price / $check->get(1)->sum);
        self::assertEquals(1, $check->get(1)->quantity);

        self::assertEquals("Вайценфельд Вайс 0,5", $check->get(2)->getName());
        self::assertEquals(2, $check->get(2)->quantity);

        self::assertEquals("test pos 0,5", $check->get(3)->getName());
        self::assertEquals(4, $check->get(3)->quantity);
    }

    public function testMerge()
    {
        $item = new CheckItem("test item", "100", "1", "100");
        $item2 = new CheckItem("test item", "100", "1", "100");

        self::assertEquals("test item", $item->getName());
        self::assertEquals(100, $item->getPrice());
        self::assertEquals(100, $item->getSum());
        self::assertEquals(1, $item->getQuantity());

        $item = $item->merge($item2);

        self::assertEquals("test item", $item->getName());
        self::assertEquals(100, $item->getPrice());
        self::assertEquals(200, $item->getSum());
        self::assertEquals(2, $item->getQuantity());

    }
}
