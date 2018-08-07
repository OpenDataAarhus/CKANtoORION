<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 * @ApiFilter(DateFilter::class, properties={"timeInstant"})
 * @ApiFilter(OrderFilter::class)
 * @ORM\Entity
 * @ORM\Table(indexes={@ORM\Index(name="search_idx", columns={"time_instant"})})
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
   * @ORM\Column(type="integer", nullable=true)
   */
  private $speedAverage;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $timeAvgMeasured;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $timeMedianMeasured;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $vehicleCount;

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId(int $id): void
  {
    $this->id = $id;
  }

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

  /**
   * @return int
   */
  public function getTimeAvgMeasured()
  {
    return $this->timeAvgMeasured;
  }

  /**
   * @param int $timeAvgMeasured
   */
  public function setTimeAvgMeasured($timeAvgMeasured)
  {
    $this->timeAvgMeasured = $timeAvgMeasured;
  }

  /**
   * @return int
   */
  public function getTimeMedianMeasured()
  {
    return $this->timeMedianMeasured;
  }

  /**
   * @param int $timeMedianMeasured
   */
  public function setTimeMedianMeasured($timeMedianMeasured)
  {
    $this->timeMedianMeasured = $timeMedianMeasured;
  }

  /**
   * @return int
   */
  public function getVehicleCount()
  {
    return $this->vehicleCount;
  }

  /**
   * @param int $vehicleCount
   */
  public function setVehicleCount($vehicleCount)
  {
    $this->vehicleCount = $vehicleCount;
  }

}
