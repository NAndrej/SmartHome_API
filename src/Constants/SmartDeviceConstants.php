<?php

namespace App\Constants;

/**
 * Konstanti koi gi sodrzhat dozvolenite vrednosti za polinjata vo objektot SmartDevice.
 */
class SmartDeviceConstants
{
    const ALLOWED_DEVICE_TYPES = [
        'light',
        'temperature',
        'blinds',
        'alarm',
    ];

    const ALLOWED_DEVICE_CATEGORY = [
        'toggle',
        'slider',
    ];  
}