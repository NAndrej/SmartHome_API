<?php

namespace App\DomainObjects;

use App\Document\SmartDevice;

class SmartDeviceFactory
{
    public static function createSmartDevice(
        string $name
    ): SmartDevice {
        $smartDevice = new SmartDevice();
        
        $smartDevice->setName($name);
        $smartDevice->setStatus(false);

        return $smartDevice;
    }
}