<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource
 * @ORM\Entity
 */
class RealTimeParkingPoint extends Point
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
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $extraSpotNumber;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $totalSpotNumber;

  /**
   * @return int
   */
  public function getExtraSpotNumber()
  {
    return $this->extraSpotNumber;
  }

  /**
   * @param int $extraSpotNumber
   */
  public function setExtraSpotNumber($extraSpotNumber)
  {
    $this->extraSpotNumber = $extraSpotNumber;
  }

  /**
   * @return int
   */
  public function getTotalSpotNumber()
  {
    return $this->totalSpotNumber;
  }

  /**
   * @param int $totalSpotNumber
   */
  public function setTotalSpotNumber($totalSpotNumber)
  {
    $this->totalSpotNumber = $totalSpotNumber;
  }

}
