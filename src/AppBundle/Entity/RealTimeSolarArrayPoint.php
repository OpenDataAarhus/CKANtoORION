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
class RealTimeSolarArrayPoint extends Point
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
  private $currentProduction;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $dailyMaxProduction;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $dailyProduction;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $totalProduction;

  /**
   * @return int
   */
  public function getCurrentProduction()
  {
    return $this->currentProduction;
  }

  /**
   * @param int $currentProduction
   */
  public function setCurrentProduction($currentProduction)
  {
    $this->currentProduction = $currentProduction;
  }

  /**
   * @return int
   */
  public function getDailyMaxProduction()
  {
    return $this->dailyMaxProduction;
  }

  /**
   * @param int $dailyMaxProduction
   */
  public function setDailyMaxProduction($dailyMaxProduction)
  {
    $this->dailyMaxProduction = $dailyMaxProduction;
  }

  /**
   * @return int
   */
  public function getDailyProduction()
  {
    return $this->dailyProduction;
  }

  /**
   * @param int $dailyProduction
   */
  public function setDailyProduction($dailyProduction)
  {
    $this->dailyProduction = $dailyProduction;
  }

  /**
   * @return int
   */
  public function getTotalProduction()
  {
    return $this->totalProduction;
  }

  /**
   * @param int $totalProduction
   */
  public function setTotalProduction($totalProduction)
  {
    $this->totalProduction = $totalProduction;
  }
}
