<?php

namespace App\Document;

use App\DTO\SmartDeviceDTO;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Strukturata na dokumentot koj bi se zapishal vo mongo. Sekoj zapis koj sakame da go zapishime vo SmartDevice kolekcijata, mora da bide objekt od ovaa klasa. 
 * Vo prodolzhenie se navedeni site polinja koi mozhe da gi sodrzhi eden dokument vo kolekcijata vo mongo.
 */

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
     * @MongoDB\Field(type="string")
     */
    protected $category;

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
    
    /**
     * @MongoDB\Field(type="raw")
     */
    protected $measuredValue;
    
    /**
     * Sekcija so geteri i seteri za pristap do privatnite polinja
     */
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

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): SmartDevice
    {
        $this->category = $category;

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

    public function getMeasuredValue(): ?string
    {
        return $this->measuredValue;
    }

    public function setMeasuredValue(?string $measuredValue = null): SmartDevice
    {
        $this->measuredValue = $measuredValue;

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

    /**
     * Konvertira objekt od SmartDevice vo asocijativna niza kade kluchevi se iminjata na polinjata, a vrednosti se vrednostite na polinjata
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'category' => $this->getCategory(),
            'value' => $this->getValue(),
            'valueType' => $this->getValueType(),
            'status' => $this->getStatus(),
            'measuredValue' => $this->getMeasuredValue(),
        ];
    }

    /**
     * Kreira DTO (Data Transfer Object) od objekt od klasata SmartDevice.
     * DTO-to se koristi za validacija na ispratenite podatoci pri opsluzhuvanje na requestot za izmena na dokument vo mongo
     */
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