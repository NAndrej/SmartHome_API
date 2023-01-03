<?php

namespace App\Document;

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
     * @MongoDB\Field(type="raw")
     */
    protected $value;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $valueType;

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
    
    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): SmartDevice
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

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'valueType' => $this->getValueType(),
        ];
    }
}