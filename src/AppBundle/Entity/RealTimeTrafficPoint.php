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
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $speedAverage;

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
