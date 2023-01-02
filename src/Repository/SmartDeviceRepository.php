<?php

namespace App\Repository;

use App\Document\SmartDevice;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

class SmartDeviceRepository extends ServiceDocumentRepository
{
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