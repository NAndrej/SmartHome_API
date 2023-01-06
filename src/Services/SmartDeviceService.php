<?php

namespace App\Services;

use App\Document\SmartDevice;
use App\DomainObjects\SmartDeviceFactory;
use App\DTO\SmartDeviceDTO;
use App\Repository\SmartDeviceRepository;

class SmartDeviceService
{
    private $smartDeviceRepository;

    public function __construct(
        SmartDeviceRepository $smartDeviceRepository,
    ) {
        $this->smartDeviceRepository = $smartDeviceRepository;
    }

    public function createFromDTO(SmartDeviceDTO $dto): SmartDevice
    {
        $smartDevice = SmartDeviceFactory::createFromDTO($dto);

        $this->smartDeviceRepository->save($smartDevice);

        return $smartDevice;
    }

    public function update(SmartDevice $smartDevice, array $data): SmartDevice
    {
        if (isset($data['value'])) {
            $smartDevice->setValue($data['value'] === false ? '0' : $data['value']);
            $smartDevice->setValueType(gettype($data['value']));
        }

        $this->smartDeviceRepository->save($smartDevice);
        
        return $smartDevice;
    }
}