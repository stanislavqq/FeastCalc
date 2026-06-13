<?php

namespace App\Service\Fiscal;

use DateTime;

class CheckItem
{
    public function __construct(
        public readonly string $name,
        public readonly int $price,
        public readonly int $quantity,
        public readonly int $sum,
    )
    {
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function withQuantityIncrement() : self
    {
        return new self(
            $this->name,
            $this->price,
            $this->quantity+1,
            $this->sum,
        );
    }
}
