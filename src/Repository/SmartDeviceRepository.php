<?php

namespace App\Repository;

use App\Document\SmartDevice;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

/**
 * Ovaa klasa sluzhi za komunikacija so mongo bazata.
 * Nasleduva od ServiceDocumentRepository, koja gi sodrzhi site opshti queries koi se pushtaat do mongo.
 */
class SmartDeviceRepository extends ServiceDocumentRepository
{
    /**
     * Vo konstruktorot se setira tipot na objekt za koi sakame ova Repository da gi izvrshuva operaciite.  
     */
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, SmartDevice::class);
    }

    public function save(SmartDevice $smartDevice): void
    {
        $this->getDocumentManager()->persist($smartDevice);
        $this->getDocumentManager()->flush();
    }
}