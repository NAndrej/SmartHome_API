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

    public function update(SmartDevice $smartDevice, array $data): SmartDevice
    {
        if (isset($data['value'])) {
            $smartDevice->setValue($data['value']);
            $smartDevice->setValueType(gettype($data['value']));
        }

        $this->smartDeviceRepository->save($smartDevice);
        
        return $smartDevice;
    }
}