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

class SmartDeviceController extends AbstractController
{
    private $smartDeviceRepository;
    private $smartDeviceService;
    private $serializer;
    private $validator;
    private $apiResponseFactory;
    private $logger;

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

    #[Route('/smart_devices', name: 'smart_devices_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $smartDevices = $this->smartDeviceRepository->findAll();

        $result = array_map(function (SmartDevice $smartDevice) {
            return $smartDevice->toArray();
        }, $smartDevices);

        $response = new JsonResponse(
            $result,
            200,
            [
                'Content-Length' => strlen(json_encode($result))
            ]
        );

        return $response;
    }

    #[Route('/smart_devices/{smartDeviceId}', name: 'smart_devices_edit', methods: ['PATCH'])]
    public function patch(Request $request, ?string $smartDeviceId = null): JsonResponse
    {
        if ($smartDeviceId === null) {
            return new JsonResponse('Bad request', 400);
        }

        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->apiResponseFactory->createBadRequestResponse('');
        }

        /** @var SmartDevice $smartDevice */
        $smartDevice = $this->smartDeviceRepository->find($smartDeviceId);

        if ($smartDevice === null) {
            return new JsonResponse('The resource does not exist', 400);
        }

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

        $validationErrors = $this->validator->validate($dto, null, 'update');
        if ($formattedValidationErrors = $this->apiResponseFactory->formatConstraintValidationErrors($validationErrors)) {
            return $this->apiResponseFactory->createValidationErrorResponse($formattedValidationErrors);
        }

        $businessLogicValidationStatus = $this->smartDeviceService->executeBusinessLogicValidation($smartDevice, $data);
        
        if (!$businessLogicValidationStatus) {
            return new JsonResponse('Bad request from business logic', 400);
        }

        $smartDevice = $this->smartDeviceService->updateFromDTO($smartDevice, $dto);

        return new JsonResponse($smartDevice->toArray());
    }
}   