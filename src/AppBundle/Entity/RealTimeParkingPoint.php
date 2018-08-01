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
   * @ORM\ManyToOne(targetEntity="RealTimeParkingAsset", inversedBy="points")
   */
  private $asset;

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
