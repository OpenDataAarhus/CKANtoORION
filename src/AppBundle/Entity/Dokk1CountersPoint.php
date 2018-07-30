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
class Dokk1CountersPoint extends Point
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
  private $visitorsIn;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $visitorsOut;

  /**
   * @return int
   */
  public function getVisitorsIn()
  {
    return $this->visitorsIn;
  }

  /**
   * @param int $visitorsIn
   */
  public function setVisitorsIn($visitorsIn)
  {
    $this->visitorsIn = $visitorsIn;
  }

  /**
   * @return int
   */
  public function getVisitorsOut()
  {
    return $this->visitorsOut;
  }

  /**
   * @param int $visitorsOut
   */
  public function setVisitorsOut($visitorsOut)
  {
    $this->visitorsOut = $visitorsOut;
  }


}
