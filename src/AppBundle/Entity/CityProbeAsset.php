<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 * @ORM\Entity
 */
class CityProbeAsset extends Asset
{
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CityProbePoint", mappedBy="asset", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"timeInstant"="ASC"})
     * @ApiSubresource
     */
    private $points;
}
