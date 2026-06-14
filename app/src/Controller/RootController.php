<?php

namespace App\Controller;

use App\Controller\DTO\QRRequestDTO;
use App\Entity\QRCheck;
use App\Service\Fiscal\FiscalService;
use App\Service\TableService;
use chillerlan\QRCode\Detector\QRCodeDetectorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class RootController extends AbstractController
{
    #[Route('/generate', name: 'app_root')]
    public function root(
        #[MapRequestPayload]
        QRRequestDTO $requestDTO,
        //Request $request,
        FiscalService $service,
        TableService $tableService,
    ): Response
    {
        try {
            $check = $service->loadCheckData($requestDTO->getFile());
        } catch (QRCodeDetectorException $e) {
            return $this->json([
                "error" => "No valid qr code",
                "message" => $e->getMessage()
            ], 400);
        }

        if (!file_exists("../tmp")) {
            mkdir("../tmp");
        }

        //dd(array_diff(scandir("../tmp"), [".", ".."]));
        foreach (array_diff(scandir("../tmp"), [".", ".."]) as $file) {
            unlink("../tmp/" . $file);
        }

        $fileSave = new \DateTimeImmutable()->format("YmdHis") . "_table.xlsx";
        $fileSavePath = "../tmp/" . $fileSave;
        $tableService
            ->setTotal($check)
            ->setPersonCount($requestDTO->getCount())
            ->setCheckItems($check->getItems())
            ->generate($fileSavePath);

        $file = new File($fileSavePath);

        $response = new BinaryFileResponse($file, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => 'attachment; filename="' . $fileSave .'"'

        ]);
        return $response;
        //return $this->redirectToRoute("app_qr_load", ["success" => true]);
    }

    #[Route('/', name: 'app_qr_load')]
    public function index(Request $request) : Response
    {
        $loadSuccess = false;
        if ($request->query->has("success")) {
            $loadSuccess = (bool) $request->query->get("success");

        }
        $qrCheck = new QRCheck();
        $form = $this->createFormBuilder($qrCheck)
            ->setAction("/generate")
            ->add('qr_file', FileType::class, [
                "label" => "Фото QR кода",
                "required" => false,
                "mapped" => false,
            ])
            ->add('count', IntegerType::class, ['label' => 'Кол-во человек'])
            ->add('save', SubmitType::class, ['label' => 'Загрузить'])
            ->getForm();

        $successMessage = "Таблица успешно сгенерированна";
        return $this->render('root/index.html.twig', compact('loadSuccess', 'successMessage', 'form'));
    }
}
