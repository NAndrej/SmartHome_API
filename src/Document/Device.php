<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * MongoDB\Device
 */
class Device
{
    /**
     * MongoDB\id
     */
    private $id;

    /**
     * MongoDB\name
     */
    private $name;

    /**
     * MongoDB\status
     */
    private $status;
}