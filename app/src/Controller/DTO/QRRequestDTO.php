<?php

namespace App\Controller\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
class QRRequestDTO
{

    public function __construct(
        #[Assert\Collection(
            fields: [
                "count" => [
                    new Assert\NotBlank(message: "count обязателен"),
                    new Assert\Regex(pattern: '/^(1[0-7]|[0-9])$/', message: "count может быть числом от 1 до 17"),
                ],
                "qr_file" => [
                    new Assert\NotBlank(message: "Qr file обязателен"),
                    new Assert\Image(maxSize: '1024k', maxSizeMessage: "Файл слишком большой")
                ],
                "save" => null,
                "_token" => null
            ]
        )]
        private array $form,
    )
    {
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): UploadedFile
    {
        return $this->form['qr_file'];
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return (int) $this->form['count'];
    }
}
