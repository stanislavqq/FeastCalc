<?php

namespace App\Entity;

class QRCheck
{
    private ?int $id = null;
    private ?string $qr = null;
    private ?int $count = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQr(): ?string
    {
        return $this->qr;
    }

    public function setQr(string $qr): static
    {
        $this->qr = $qr;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }
}
