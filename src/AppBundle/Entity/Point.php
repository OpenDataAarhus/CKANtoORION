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
   * @var \DateTime
   * @ORM\Column(type="datetime")
   */
  protected $timeInstant;

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
