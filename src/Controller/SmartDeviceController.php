<?php

namespace App\Controller;

use App\Document\SmartDevice;
use App\DomainObjects\SmartDeviceFactory;
use App\Repository\SmartDeviceRepository;
use App\Services\SmartDeviceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;

class SmartDeviceController extends AbstractController
{
    private $smartDeviceRepository;
    private $smartDeviceService;

    public function __construct(
        SmartDeviceRepository $smartDeviceRepository,
        SmartDeviceService $smartDeviceService,
    ) {
        $this->smartDeviceRepository = $smartDeviceRepository;
        $this->smartDeviceService = $smartDeviceService;
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
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (\json_last_error() !== 0) {
            return new Response('Bad request', 400);
        }

        $name = isset($data['name']) ? strip_tags(trim($data['name'])) : '';

        $smartDevice = SmartDeviceFactory::createSmartDevice($name);
        $this->smartDeviceRepository->save($smartDevice);

        return new JsonResponse($smartDevice->toArray());
    }

    #[Route('/api/smart_devices/{smartDeviceId}', name: 'smart_devices_edit', methods: ['PATCH'])]
    public function patch(Request $request, ?string $smartDeviceId = null): JsonResponse
    {
        if ($smartDeviceId === null) {
            return new Response('Bad Request', 400);
        }

        $smartDevice = $this->smartDeviceRepository->find($smartDeviceId);

        if ($smartDevice === null) {
            return new Response('The resource does not exist', 404);
        }

        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== 0) {
            return new Response('Bad request', 400);
        }

        $smartDevice = $this->smartDeviceService->upsert($smartDevice, $data);

        return new JsonResponse($smartDevice->toArray());
    }
}