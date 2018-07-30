<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
abstract class Point
{
  /**
   * @var int The entity Id
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="Asset", inversedBy="points")
   */
  protected $asset;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime")
   */
  protected $timeInstant;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id)
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
   * @return \DateTime
   */
  public function getTimeInstant()
  {
    return $this->timeInstant;
  }

  /**
   * @param \DateTime $timeInstant
   */
  public function setTimeInstant($timeInstant)
  {
    $this->timeInstant = $timeInstant;
  }
}
