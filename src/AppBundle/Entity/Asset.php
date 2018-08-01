<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 */
abstract class Asset
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
   * @var string Something else
   *
   * @ORM\Column
   * @Assert\NotBlank
   */
  private $identifier = '';

  /**
   * @var string the name of the item
   *
   * @ORM\Column(nullable=true)
   * @Assert\Type(type="string")
   * @ApiProperty(iri="https://schema.org/name")
   */
  private $name;

  /**
   * @var string the type of the item
   *
   * @ORM\Column(nullable=true)
   * @Assert\Type(type="string")
   * @ApiProperty(iri="https://schema.org/name")
   */
  private $type;

  /**
   * @var string the URI of the item
   *
   * @ORM\Column(nullable=true)
   * @Assert\Type(type="string")
   * @ApiProperty(iri="http://schema.org/url")
   */
  private $url;

  /**
   * @var number The latitude of the location
   *
   * @ORM\Column(nullable=true, type="float")
   */
  private $latitude;

  /**
   * @var number The longitude of the location
   *
   * @ORM\Column(nullable=true, type="float")
   */
  private $longitude;

  /**
   * @var ArrayCollection
   */
  private $points;


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
  public function setId($id): void
  {
    $this->id = $id;
  }

  public function getIdentifier(): string
  {
    return $this->identifier;
  }

  public function setIdentifier(string $identifier)
  {
    $this->identifier = $identifier;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }

  /**
   * @return number
   */
  public function getLatitude()
  {
    return $this->latitude;
  }

  /**
   * @param number $latitude
   */
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }

  /**
   * @return number
   */
  public function getLongitude()
  {
    return $this->longitude;
  }

  /**
   * @param number $longitude
   */
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }

  /**
   * @return ArrayCollection
   */
  public function getPoints()
  {
    return $this->points;
  }

  /**
   * @param ArrayCollection $points
   */
  public function setPoints($points)
  {
    $this->points = $points;
  }
}
