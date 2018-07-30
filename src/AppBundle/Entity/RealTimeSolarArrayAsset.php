<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource
 * @ORM\Entity
 */
class RealTimeSolarArrayAsset extends Asset
{
  /**
   * @var ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="RealTimeSolarArrayPoint", mappedBy="asset", cascade={"persist"}, orphanRemoval=true)
   * @ORM\OrderBy({"timeInstant"="ASC"})
   * @ApiSubresource
   */
  private $points;

}
