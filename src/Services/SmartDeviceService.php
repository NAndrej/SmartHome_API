<?php

namespace App\Services;

use App\Document\SmartDevice;
use App\Repository\SmartDeviceRepository;

class SmartDeviceService
{
    private $smartDeviceRepository;

    public function __construct(
        SmartDeviceRepository $smartDeviceRepository,
    ) {
        $this->smartDeviceRepository = $smartDeviceRepository;
    }

    public function upsert(SmartDevice $smartDevice, array $data): SmartDevice
    {
        //TODO: Refactor when more fields are added. Also include validation
        if (
            isset($data['status']) 
            && is_bool($data['status'])
        ) {
            $smartDevice->setStatus($data['status']);
        }

        $this->smartDeviceRepository->save($smartDevice);
        
        return $smartDevice;
    }
}