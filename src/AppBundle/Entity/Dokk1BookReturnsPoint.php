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
class Dokk1BookReturnsPoint extends Point
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
  private $returnsPast24hours;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $returnsPast5min;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $returnsPast60min;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $returnsToday;

  /**
   * @return int
   */
  public function getReturnsPast24hours()
  {
    return $this->returnsPast24hours;
  }

  /**
   * @param int $returnsPast24hours
   */
  public function setReturnsPast24hours($returnsPast24hours)
  {
    $this->returnsPast24hours = $returnsPast24hours;
  }

  /**
   * @return int
   */
  public function getReturnsPast5min()
  {
    return $this->returnsPast5min;
  }

  /**
   * @param int $returnsPast5min
   */
  public function setReturnsPast5min($returnsPast5min)
  {
    $this->returnsPast5min = $returnsPast5min;
  }

  /**
   * @return int
   */
  public function getReturnsPast60min()
  {
    return $this->returnsPast60min;
  }

  /**
   * @param int $returnsPast60min
   */
  public function setReturnsPast60min($returnsPast60min)
  {
    $this->returnsPast60min = $returnsPast60min;
  }

  /**
   * @return int
   */
  public function getReturnsToday()
  {
    return $this->returnsToday;
  }

  /**
   * @param int $returnsToday
   */
  public function setReturnsToday($returnsToday)
  {
    $this->returnsToday = $returnsToday;
  }
}
