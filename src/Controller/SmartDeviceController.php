<?php

namespace App\Controller;

use App\Document\SmartDevice;
use App\DomainObjects\SmartDeviceFactory;
use App\DTO\SmartDeviceDTO;
use App\Repository\SmartDeviceRepository;
use App\Services\ApiResponse;
use App\Services\ApiResponseFactory;
use App\Services\SmartDeviceService;
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

    public function __construct(
        SmartDeviceRepository $smartDeviceRepository,
        SmartDeviceService $smartDeviceService,
        SerializerInterface $serializerInterface,
        ValidatorInterface $validatorInterface,
        ApiResponseFactory $apiResponseFactory,
    ) {
        $this->smartDeviceRepository = $smartDeviceRepository;
        $this->smartDeviceService = $smartDeviceService;
        $this->serializer = $serializerInterface;
        $this->validator = $validatorInterface;
        $this->apiResponseFactory = $apiResponseFactory;
    }

    #[Route('/api/smart_devices', name: 'smart_devices_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $smartDevices = $this->smartDeviceRepository->findAll();

        $result = array_map(function (SmartDevice $smartDevice) {
            return $smartDevice->toArray();
        }, $smartDevices);

        return new JsonResponse($result);
    }

    #[Route('/api/smart_devices', name: 'smart_devices_create', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->apiResponseFactory->createBadRequestResponse('');
        }

        try {
            $dto = $this->serializer->deserialize(
                json_encode($data),
                SmartDeviceDTO::class,
                'json',
                ['groups' => 'create']
            );
        } catch (\Exception $e) {
            return $this->apiResponseFactory->createBadRequestResponse('');
        }
    
        $validationErrors = $this->validator->validate($dto, null, 'create');
        if ($formattedValidationErrors = $this->apiResponseFactory->formatConstraintValidationErrors($validationErrors)) {
            return $this->apiResponseFactory->createValidationErrorResponse($formattedValidationErrors);
        }

        $smartDevice = $this->smartDeviceService->createFromDTO($dto);

        return new JsonResponse($smartDevice->toArray());
    }

    #[Route('/api/smart_devices/{smartDeviceId}', name: 'smart_devices_edit', methods: ['PATCH'])]
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

        if (!isset($data['value'])) {
            $validationErrors = $this->validator->validate($dto, null, 'update_without_value');
        } else {
            $validationErrors = $this->validator->validate($dto, null, 'update');
        }
        if ($formattedValidationErrors = $this->apiResponseFactory->formatConstraintValidationErrors($validationErrors)) {
            return $this->apiResponseFactory->createValidationErrorResponse($formattedValidationErrors);
        }

        $smartDevice = $this->smartDeviceService->updateFromDTO($smartDevice, $dto);

        return new JsonResponse($smartDevice->toArray());
    }
}