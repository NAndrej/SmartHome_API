<?php

namespace App\DomainObjects;

use App\Document\SmartDevice;

class SmartDeviceFactory
{
    public static function createSmartDevice(
        string $name,
        string $type,
        $value,
    ): SmartDevice {
        $smartDevice = new SmartDevice();

        $smartDevice->setName($name);
        $smartDevice->setType($type);
        $smartDevice->setValue($value === false ? '0' : $value);
        $smartDevice->setValueType(gettype($value));

        return $smartDevice;
    }
}