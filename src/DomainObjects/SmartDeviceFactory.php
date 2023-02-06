<?php

namespace App\DomainObjects;

use App\Document\SmartDevice;
use App\DTO\SmartDeviceDTO;

class SmartDeviceFactory
{
    public static function createFromDTO(
        SmartDeviceDTO $dto,
    ): SmartDevice {
        $smartDevice = new SmartDevice();

        $smartDevice->setName($dto->name);
        $smartDevice->setType($dto->type);
        $smartDevice->setValue($dto->value === false ? '0' : $dto->value);
        $smartDevice->setValueType(gettype($dto->value));
        $smartDevice->setStatus($dto->status);
        $smartDevice->setCategory($dto->category);

        return $smartDevice;
    }
}