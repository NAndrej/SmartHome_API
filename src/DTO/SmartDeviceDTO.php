<?php

namespace App\DTO;

use App\Constants\SmartDeviceConstants;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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