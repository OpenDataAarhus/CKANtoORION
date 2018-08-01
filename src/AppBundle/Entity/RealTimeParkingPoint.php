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
