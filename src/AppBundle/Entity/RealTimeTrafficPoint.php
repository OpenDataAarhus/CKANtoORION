<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 * @ORM\Entity
 */
class RealTimeTrafficPoint extends Point
{
  /**
   * @var int The entity Id
   *
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="RealTimeTrafficAsset", inversedBy="points")
   */
  private $asset;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $speedAverage;

  /**
   * @return mixed
   */
  public function getAsset()
  {
    return $this->asset;
  }

  /**
   * @param mixed $asset
   */
  public function setAsset($asset)
  {
    $this->asset = $asset;
  }

  /**
   * @return int
   */
  public function getSpeedAverage()
  {
    return $this->speedAverage;
  }

  /**
   * @param int $speedAverage
   */
  public function setSpeedAverage($speedAverage)
  {
    $this->speedAverage = $speedAverage;
  }

}
