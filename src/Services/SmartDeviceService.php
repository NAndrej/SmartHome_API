<?php

namespace App\Services;

use App\Document\SmartDevice;
use App\DomainObjects\SmartDeviceFactory;
use App\DTO\SmartDeviceDTO;
use App\Repository\SmartDeviceRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

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

    public function updateFromDTO(SmartDevice $smartDevice, SmartDeviceDTO $dto): SmartDevice
    {
        $serializerClassMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(new AnnotationReader())
        );
        $serializerExtractor = new SerializerExtractor($serializerClassMetadataFactory);
        
        $properties = $serializerExtractor
            ->getProperties(SmartDeviceDTO::class, ['serializer_groups' => ['update']]);

        foreach ($properties as $property) {
            $value = $dto->{$property};

            //TODO: Can be done ValidatorInterface and dynamic validation. Check smfcon
            if (
                (
                    $property === 'value'
                    && gettype($value) !== $smartDevice->getValueType()
                )
                || (
                    $property === 'measuredValue'
                    && gettype($value) !== $smartDevice->getValueType()
                )
            ) {
                continue;
            }

            if (
                $property === 'value'
                && $value === false
            ) {
                $smartDevice->setValue('0');

                continue;
            }

            $smartDevice->{'set' . ucfirst($property)}($value);
        }

        $this->smartDeviceRepository->save($smartDevice);
        
        return $smartDevice;
    }
}