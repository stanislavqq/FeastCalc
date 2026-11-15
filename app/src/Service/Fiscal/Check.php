<?php

namespace App\Service\Fiscal;

class Check
{

    public function __construct(
        public readonly \DateTimeImmutable $dateTime,
        public readonly int $totalSum,
        private array $items = [],
    )
    {
    }

    public static function parseJson(string $json): static
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            // TODO: log and throw
            throw $e;
        }

        return Check::parseArray($data);
    }

    public static function parseArray(array $data): static
    {
        /** @var CheckItem[] $checkItems */
        $checkItems = [];
        foreach ($data["items"] as $item) {

            $quantity = $item["quantity"];
            if (isset($item['itemsQuantityMeasure']) && $item['itemsQuantityMeasure'] == 41) {

                $matches = [];
                preg_match_all('/\d{1,2}[.,]?\d{,2}/', $item["name"], $matches);

                if (isset($matches[0][0])) {
                    $number = $matches[0][0];
                    $number = (float) str_replace(",", ".", $number);

                    if ($number > 0) {
                        $quantity = (int) ($item["quantity"] / $number);
                    }
                }
            }

            if (isset($checkItems[$item["name"]])) {
                $checkItems[$item["name"]] = $checkItems[$item["name"]]->merge(new CheckItem(
                    $item["name"],
                    $item["price"],
                    $quantity,
                    $item["sum"],
                ));
            } else {
                $checkItems[$item["name"]] = new CheckItem(
                    $item["name"],
                    $item["price"],
                    $quantity,
                    $item["sum"],
                );
            }
        }

        $dateTime = new \DateTimeImmutable($data["dateTime"]);
        return new static($dateTime, $data["totalSum"], $checkItems);
    }

    public function get(int $offset) : ?CheckItem
    {
        $values = array_values($this->items);

        if (isset($values[$offset])) {
            return $values[$offset];
        }

        return null;
    }

    public function getDateTime() : \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getTotalSum(): int
    {
        return $this->totalSum;
    }

    public function getItemsCount(): int
    {
        return count($this->items);
    }

    /**
     * @return CheckItem[]
     */
    public function getItems() : array
    {
        return array_values($this->items);
    }
}
