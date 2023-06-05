<?php

namespace App\Controller;

use App\Document\SmartDevice;
use App\DomainObjects\SmartDeviceFactory;
use App\DTO\SmartDeviceDTO;
use App\Repository\SmartDeviceRepository;
use App\Services\ApiResponse;
use App\Services\ApiResponseFactory;
use App\Services\SmartDeviceService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse };
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * API Kontroler koj gi opsluzhuva requestite koi doagjaat na /api/smart_devices endpointite.
 * Poddrzhuva GET request na /api/smart_devices endpointot, i PATCH na /api/smart_devices/{id} endpointot.
 */
class SmartDeviceController extends AbstractController
{
    /**
     * Deklaracija na privatni properties
     */
    private $smartDeviceRepository;
    private $smartDeviceService;
    private $serializer;
    private $validator;
    private $apiResponseFactory;
    private $logger;

    /**
     * Dependency injection. Vo konstruktorot na ovaj kontroler se inicijaliziraat servisite i klasite koi se koristat vo ovoj kontroler.
     */
    public function __construct(
        SmartDeviceRepository $smartDeviceRepository,
        SmartDeviceService $smartDeviceService,
        SerializerInterface $serializerInterface,
        ValidatorInterface $validatorInterface,
        ApiResponseFactory $apiResponseFactory,
        LoggerInterface $loggerInterface,
    ) {
        $this->smartDeviceRepository = $smartDeviceRepository;
        $this->smartDeviceService = $smartDeviceService;
        $this->serializer = $serializerInterface;
        $this->validator = $validatorInterface;
        $this->apiResponseFactory = $apiResponseFactory;
        $this->logger = $loggerInterface;
    }

    /**
     * Vo ovaa funkcija se opsluzhuva GET requestot koj doagja na /api/smart_devices
     * Sluzhi za listanje na site zapisi koi se naogjaat vo mongo kolekcijata smart_devices
     */
    #[Route('/api/smart_devices', name: 'smart_devices_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        // Zemanje na site dokumenti koi se zapishani vo mongo kolekcijata.
        $smartDevices = $this->smartDeviceRepository->findAll();

        // Konvertiranje na objektite dobieni od mongo vo nizi za da se vratat vo response-ot na request-ot.
        $result = array_map(function (SmartDevice $smartDevice) {
            return $smartDevice->toArray();
        }, $smartDevices);

        return new JsonResponse($result);
    }

    /**
     * Vo ovaa funkcija se opsluzhuva PATCH requestot koj doagja na /api/smart_devices/{id}
     * Sluzhi za izmenuvanje na poseben zapis vo baza. 
     * Kako path parametar se prima id-to na uredot koj sakame da go izmenime, a ochekuva request body koe gi sodrzhi polinjata od objektot koi treba da se izmenat  
     */
    #[Route('/api/smart_devices/{smartDeviceId}', name: 'smart_devices_edit', methods: ['PATCH'])]
    public function patch(Request $request, ?string $smartDeviceId = null): JsonResponse
    {
        // Vo slednite nekolku linii se validiraat vleznite parametri pushteni vo requestot, i se vrakja soodveten response so soodveten status code ako neshto ne e vo red.
        if ($smartDeviceId === null) {
            return new JsonResponse('Bad request', 400);
        }

        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->apiResponseFactory->createBadRequestResponse('');
        }

        // Zemanje na dokument od mongo. Tuka smartDeviceId vekje e string koj mozhe da probame da go najdime vo baza.
        /** @var SmartDevice $smartDevice */
        $smartDevice = $this->smartDeviceRepository->find($smartDeviceId);

        // Ako ne postoi dokument vo baza so toj id, se vrakja response so status kod 400.
        if ($smartDevice === null) {
            return new JsonResponse('The resource does not exist', 400);
        }

        // Konvertiranje na request body-to vo SmartDeviceDTO, so cel ponatamoshna validacija na ispratenite podatoci.
        try {
            $dto = $this->serializer->deserialize(
                json_encode($data),
                SmartDeviceDTO::class,
                'json',
                [
                    'groups' => 'update',
                    AbstractNormalizer::OBJECT_TO_POPULATE => $smartDevice->getDTO(),
                ]
            );
        } catch (\Exception $e) {
            return $this->apiResponseFactory->createBadRequestResponse('');
        }

        // Validacija na kreiraniot DTO (Data Transfer Object), i vrakjanje na soodveten JsonResponse ako padni validacijata.
        if (!isset($data['value'])) {
            $validationErrors = $this->validator->validate($dto, null, 'update_without_value');
        } else {
            $validationErrors = $this->validator->validate($dto, null, 'update');
        }
        if ($formattedValidationErrors = $this->apiResponseFactory->formatConstraintValidationErrors($validationErrors)) {
            return $this->apiResponseFactory->createValidationErrorResponse($formattedValidationErrors);
        }

        $businessLogicValidationStatus = $this->smartDeviceService->executeBusinessLogicValidation($smartDevice, $data);
        
        if (!$businessLogicValidationStatus) {
            return new JsonResponse('Bad request from business logic', 400);
        }

        // Izmena na zapisot vo baza.
        $smartDevice = $this->smartDeviceService->updateFromDTO($smartDevice, $dto);

        // Requestot e uspeshen, se vrakja izmenetiot zapis nazad.
        return new JsonResponse($smartDevice->toArray());
    }
}   