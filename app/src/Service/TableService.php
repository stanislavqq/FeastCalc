<?php

namespace App\Service;

use App\Service\Fiscal\Check;
use App\Service\Fiscal\CheckItem;
use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TableService
{
    private Worksheet $worksheet;
    private Spreadsheet $spreadsheet;
    public function __construct(
        #[Autowire(env: "TABLE_TEMPLATE")]
        string $tableTemplate,
    )
    {
        if (!file_exists($tableTemplate)) {
            throw new \InvalidArgumentException("Table template does not exist");
        }

        $this->spreadsheet = IOFactory::load($tableTemplate);
        $this->worksheet = $this->spreadsheet->getActiveSheet();
    }

    public function generate(string $savePath) : void
    {
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xls');
        $writer->save($savePath);
    }
    public function setPersonCount(int $count): self
    {
        if ($count > 0) {
            $this->worksheet->setCellValue("B70", $count);
        }

        if ($count <= 1) {
            return $this;
        }

        $dataArray = $this->worksheet->rangeToArray(
            "F1:G68",
            NULL,   // Null value for empty cells
            false,   // Return formulas as calculated values
        );

        //$refHelper = ReferenceHelper::getInstance();

        for ($i = 8, $c = 0; $c < $count-1; $i+=2, $c++) {


//            for ($j = 2; $j < count($dataArray)- 1; $j++) {
//                $row = $dataArray[$j];
//                $formula = $refHelper->updateFormulaReferences(
//                    $row[1],
//                    Coordinate::stringFromColumnIndex(7) . "3",
//                    2
//                );
//            }

            $this->worksheet->fromArray(
                $dataArray,
                null,
                Coordinate::stringFromColumnIndex($i) . "1",
            );
        }

        return $this;
    }

    public function setTotal(Check $check): self
    {
        $this->worksheet->getCell('D69')->setValue($check->getTotalSum()/100);
        return $this;
    }

    /**
     * @param CheckItem[] $items
     * @return $this
     */
    public function setCheckItems(array $items): self
    {
        foreach ($items as $key => $checkItem) {
            $cellNum = $key + 3;
            $this->worksheet->getCell('A' . $cellNum)->setValue($checkItem->getName());
            $this->worksheet->getCell('B' . $cellNum)->setValue($checkItem->price / 100);
            $this->worksheet->getCell('C' . $cellNum)->setValue($checkItem->quantity);
        }

        return $this;
    }
}
