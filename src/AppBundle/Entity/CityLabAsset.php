<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
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
class CityLabAsset extends Asset
{
  /**
   * @var ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="CityLabPoint", mappedBy="asset", cascade={"persist"}, orphanRemoval=true)
   * @ORM\OrderBy({"timeInstant"="ASC"})
   * @ApiSubresource
   */
  private $points;

}
