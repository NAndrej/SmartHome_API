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
    /**
     * Deklaracija na privatni properties
     */
    private $smartDeviceRepository;

    /**
     * Dependency injection. Vo konstruktorot na ovaj kontroler se inicijaliziraat servisite i klasite koi se koristat vo ovoj kontroler.
     */
    public function __construct(
        SmartDeviceRepository $smartDeviceRepository,
    ) {
        $this->smartDeviceRepository = $smartDeviceRepository;
    }

    /**
     * Funkcija koja pravi izmeni vo objekt od SmartDevice od vekje validiran SmartDeviceDTO.
     */
    public function updateFromDTO(SmartDevice $smartDevice, SmartDeviceDTO $dto): SmartDevice
    {
        /**
         * Prvichno se zemaat site properties koi gi ima SmartDeviceDTO objektot, so cel podocna da se izednachat vrednostite koi gi imaat DTO-to i samiot objekt od SmartDevice.
         */
        $serializerClassMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(new AnnotationReader())
        );
        $serializerExtractor = new SerializerExtractor($serializerClassMetadataFactory);
        
        $properties = $serializerExtractor
            ->getProperties(SmartDeviceDTO::class, ['serializer_groups' => ['update']]);

        /**
         * Iteracija na properties i dodeluvanje na vrednostite vo SmartDevice ovbjektot.
         */
        foreach ($properties as $property) {
            $value = $dto->{$property};

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

        /**
         * Zachuvuvanje na izmenetiot SmartDevice objekt vo baza
         */
        $this->smartDeviceRepository->save($smartDevice);
        
        return $smartDevice;
    }
}