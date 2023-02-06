<?php

namespace App\Document;

use App\DTO\SmartDeviceDTO;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class SmartDevice
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $type;

    /**
     * @MongoDB\Field(type="raw")
     */
    protected $value;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $valueType;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $status;

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): SmartDevice
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): SmartDevice
    {
        $this->type = $type;

        return $this;
    }
    
    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): SmartDevice
    {
        $this->value = $value;

        return $this;
    }

    public function getValueType(): string
    {
        return $this->valueType;
    }

    public function setValueType(string $valueType): SmartDevice
    {
        $this->valueType = $valueType;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status = null): SmartDevice
    {
        $this->status = $status;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'value' => $this->getValue(),
            'valueType' => $this->getValueType(),
            'status' => $this->getStatus(),
        ];
    }

    public function getDTO(): SmartDeviceDTO
    {
        $dto = new SmartDeviceDTO();

        $objectProperties = new \ReflectionClass(SmartDevice::class);
        $objectProperties = array_map(function ($objectPropertyInfo) {
                return $objectPropertyInfo->name;
        }, $objectProperties->getProperties());
        
        foreach ($objectProperties as $property) {
            $dto->{$property} = $this->{'get' . ucfirst($property)}();
        }

        return $dto;
    }
}