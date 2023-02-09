<?php

namespace App\DTO;

use App\Constants\SmartDeviceConstants;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object za klasata SmartDevice
 * Sluzhi za validacija na vrednostite pred da se dodelat na nekoj SmartDevice objekt, so cel nikogash da nemame nevalidni podatoci vo baza.
 * 
 * Validacijata se izvrshuva so razlichni asserti.
 * Assert\Type sluzhi za validacija deka podatokot e od nekoj tip.
 * Assert\Choice sluzhi za validacija deka podatokot e nekoj od dozvolenite vrednosti.
 * Assert\NotBlank sluzhi za validacija deka podatokot ne e null ili prazen string.
 * 
 * Groups sluzhi za dodeluvanje na grupa na property-to.
 * Sluzhi za podelba na properties vo povekje grupi, so cel polesno manipuliranje vo deserializacijata i serializacijata. 
 */
class SmartDeviceDTO
{
    public $id;

    #[Groups(['create', 'update', 'update_without_value'])]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Type('string', groups: ['create', 'update', 'update_without_value'])]
    public $name;

    #[Groups(['create', 'update', 'update_without_value'])]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Type('string', groups: ['create', 'update', 'update_without_value'])]
    #[Assert\Choice(SmartDeviceConstants::ALLOWED_DEVICE_TYPES,  groups: ['create', 'update', 'update_without_value'])]
    public $type;

    #[Groups(['create'])]
    #[Assert\Type('string', groups: ['create'])]
    #[Assert\Choice(SmartDeviceConstants::ALLOWED_DEVICE_CATEGORY,  groups: ['create'])]
    public $category;

    #[Groups(['create', 'update', 'update_without_value'])]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Type(type: ['boolean', 'float', 'integer'], groups: ['create', 'update'])]
    #[Assert\Type(type: ['string'], groups: ['update_without_value'])]
    public $value;

    public $valueType;

    #[Groups(['create', 'update', 'update_without_value'])]
    #[Assert\Type('boolean', groups: ['update', 'update_without_value'])]
    public $status;

    #[Groups(['create', 'update', 'update_without_value'])]
    #[Assert\Type('string', groups: ['update', 'update_without_value'])]
    public $measuredValue;
}