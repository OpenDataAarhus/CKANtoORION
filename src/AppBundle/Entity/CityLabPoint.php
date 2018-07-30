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
class CityLabPoint extends Point
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
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $atmosphericPressure;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $batteryLevel;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $chargingPower;

  /**
   * @var int
   *
   * @ORM\Column(type="smallint", nullable=true)
   */
  private $heightAboveMeanSeaLevel;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $outsideHumidity;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $outsideTemperature;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $waterTemperature;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $daylight;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $rainfall;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $sunlightPAR;

  /**
   * @var string
   *
   * @ORM\Column(type="string", length=3, nullable=true)
   */
  private $windDirection;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $windSpeed;

  /**
   * @return float
   */
  public function getAtmosphericPressure()
  {
    return $this->atmosphericPressure;
  }

  /**
   * @param float $atmosphericPressure
   */
  public function setAtmosphericPressure($atmosphericPressure)
  {
    $this->atmosphericPressure = $atmosphericPressure;
  }

  /**
   * @return float
   */
  public function getBatteryLevel()
  {
    return $this->batteryLevel;
  }

  /**
   * @param float $batteryLevel
   */
  public function setBatteryLevel($batteryLevel)
  {
    $this->batteryLevel = $batteryLevel;
  }

  /**
   * @return int
   */
  public function getChargingPower()
  {
    return $this->chargingPower;
  }

  /**
   * @param int $chargingPower
   */
  public function setChargingPower($chargingPower)
  {
    $this->chargingPower = $chargingPower;
  }

  /**
   * @return int
   */
  public function getHeightAboveMeanSeaLevel()
  {
    return $this->heightAboveMeanSeaLevel;
  }

  /**
   * @param int $heightAboveMeanSeaLevel
   */
  public function setHeightAboveMeanSeaLevel($heightAboveMeanSeaLevel)
  {
    $this->heightAboveMeanSeaLevel = $heightAboveMeanSeaLevel;
  }

  /**
   * @return float
   */
  public function getOutsideHumidity()
  {
    return $this->outsideHumidity;
  }

  /**
   * @param float $outsideHumidity
   */
  public function setOutsideHumidity($outsideHumidity)
  {
    $this->outsideHumidity = $outsideHumidity;
  }

  /**
   * @return float
   */
  public function getOutsideTemperature()
  {
    return $this->outsideTemperature;
  }

  /**
   * @param float $outsideTemperature
   */
  public function setOutsideTemperature($outsideTemperature)
  {
    $this->outsideTemperature = $outsideTemperature;
  }

  /**
   * @return float
   */
  public function getWaterTemperature()
  {
    return $this->waterTemperature;
  }

  /**
   * @param float $waterTemperature
   */
  public function setWaterTemperature($waterTemperature)
  {
    $this->waterTemperature = $waterTemperature;
  }

  /**
   * @return int
   */
  public function getDaylight()
  {
    return $this->daylight;
  }

  /**
   * @param int $daylight
   */
  public function setDaylight($daylight)
  {
    $this->daylight = $daylight;
  }

  /**
   * @return int
   */
  public function getRainfall()
  {
    return $this->rainfall;
  }

  /**
   * @param int $rainfall
   */
  public function setRainfall($rainfall)
  {
    $this->rainfall = $rainfall;
  }

  /**
   * @return float
   */
  public function getSunlightPAR()
  {
    return $this->sunlightPAR;
  }

  /**
   * @param float $sunlightPAR
   */
  public function setSunlightPAR($sunlightPAR)
  {
    $this->sunlightPAR = $sunlightPAR;
  }

  /**
   * @return string
   */
  public function getWindDirection()
  {
    return $this->windDirection;
  }

  /**
   * @param string $windDirection
   */
  public function setWindDirection($windDirection)
  {
    $this->windDirection = $windDirection;
  }

  /**
   * @return int
   */
  public function getWindSpeed()
  {
    return $this->windSpeed;
  }

  /**
   * @param int $windSpeed
   */
  public function setWindSpeed($windSpeed)
  {
    $this->windSpeed = $windSpeed;
  }
}