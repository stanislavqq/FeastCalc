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

    public static function parseArray(array $data): static
    {
        /** @var CheckItem[] $checkItems */
        $checkItems = [];
        foreach ($data["items"] as $item) {
            if (isset($checkItems[$item["name"]])) {
                $checkItems[$item["name"]] = $checkItems[$item["name"]]->withQuantityIncrement();
            } else {
                $checkItems[$item["name"]] = new CheckItem(
                    $item["name"],
                    $item["price"],
                    $item["quantity"],
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
