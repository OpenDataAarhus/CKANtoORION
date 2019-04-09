<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
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
class CityProbePoint extends Point
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="CityProbeAsset", inversedBy="points")
     */
    private $asset;

    /**
     * @var json
     *
     * @ORM\Column(name="noise", type="json")
     */
    private $noise;

    /**
     * @var int
     *
     * @ORM\Column(name="co", type="integer")
     */
    private $co;

    /**
     * @var float
     *
     * @ORM\Column(name="temperature", type="float")
     */
    private $temperature;

    /**
     * @var int
     *
     * @ORM\Column(name="pm10", type="integer")
     */
    private $pm10;

    /**
     * @var float
     *
     * @ORM\Column(name="battery", type="float")
     */
    private $battery;

    /**
     * @var json
     *
     * @ORM\Column(name="rain", type="json")
     */
    private $rain;

    /**
     * @var float
     *
     * @ORM\Column(name="humidity", type="float")
     */
    private $humidity;

    /**
     * @var int
     *
     * @ORM\Column(name="illuminance", type="integer")
     */
    private $illuminance;

    /**
     * @var float
     *
     * @ORM\Column(name="atmosphericPressure", type="float")
     */
    private $atmosphericPressure;

    /**
     * @var int
     *
     * @ORM\Column(name="pm25", type="integer")
     */
    private $pm25;

    /**
     * @var int
     *
     * @ORM\Column(name="no2", type="integer")
     */
    private $no2;

    /**
     * @var int
     *
     * @ORM\Column(name="firmwareVersion", type="integer", nullable=true)
     */
    private $firmwareVersion;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return CityProbePoint
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Asset.
     *
     * @return CityProbeAsset
     */
    public function getAsset(): CityProbeAsset
    {
        return $this->asset;
    }

    /**
     * Set Asset.
     *
     * @param CityProbeAsset $asset
     *
     * @return CityProbePoint
     */
    public function setAsset(CityProbeAsset $asset): self
    {
        $this->asset = $asset;

        return $this;
    }

    /**
     * Set noise.
     *
     * @param json $noise
     *
     * @return CityProbePoint
     */
    public function setNoise($noise)
    {
        $this->noise = $noise;

        return $this;
    }

    /**
     * Get noise.
     *
     * @return json
     */
    public function getNoise()
    {
        return $this->noise;
    }

    /**
     * Set co.
     *
     * @param int $co
     *
     * @return CityProbePoint
     */
    public function setCo(int $co): self
    {
        $this->co = $co;

        return $this;
    }

    /**
     * Get co.
     *
     * @return int
     */
    public function getCo(): int
    {
        return $this->co;
    }

    /**
     * Set temperature.
     *
     * @param float $temperature
     *
     * @return CityProbePoint
     */
    public function setTemperature(float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Get temperature.
     *
     * @return float
     */
    public function getTemperature(): float
    {
        return $this->temperature;
    }

    /**
     * Set pm10.
     *
     * @param int $pm10
     *
     * @return CityProbePoint
     */
    public function setPm10(int $pm10): self
    {
        $this->pm10 = $pm10;

        return $this;
    }

    /**
     * Get pm10.
     *
     * @return int
     */
    public function getPm10(): int
    {
        return $this->pm10;
    }

    /**
     * Set battery.
     *
     * @param float $battery
     *
     * @return CityProbePoint
     */
    public function setBattery(float $battery): self
    {
        $this->battery = $battery;

        return $this;
    }

    /**
     * Get battery.
     *
     * @return float
     */
    public function getBattery(): float
    {
        return $this->battery;
    }

    /**
     * Set rain.
     *
     * @param json $rain
     *
     * @return CityProbePoint
     */
    public function setRain($rain): self
    {
        $this->rain = $rain;

        return $this;
    }

    /**
     * Get rain.
     *
     * @return json
     */
    public function getRain()
    {
        return $this->rain;
    }

    /**
     * Set humidity.
     *
     * @param float $humidity
     *
     * @return CityProbePoint
     */
    public function setHumidity(float $humidity): self
    {
        $this->humidity = $humidity;

        return $this;
    }

    /**
     * Get humidity.
     *
     * @return float
     */
    public function getHumidity(): float
    {
        return $this->humidity;
    }

    /**
     * Set illuminance.
     *
     * @param int $illuminance
     *
     * @return CityProbePoint
     */
    public function setIlluminance(int $illuminance): self
    {
        $this->illuminance = $illuminance;

        return $this;
    }

    /**
     * Get illuminance.
     *
     * @return int
     */
    public function getIlluminance(): int
    {
        return $this->illuminance;
    }

    /**
     * Set pressure.
     *
     * @param float $atmosphericPressure
     *
     * @return CityProbePoint
     */
    public function setAtmosphericPressure(float $atmosphericPressure): self
    {
        $this->atmosphericPressure = $atmosphericPressure;

        return $this;
    }

    /**
     * Get pressure.
     *
     * @return float
     */
    public function getAtmosphericPressure(): float
    {
        return $this->atmosphericPressure;
    }

    /**
     * Set pm25.
     *
     * @param int $pm25
     *
     * @return CityProbePoint
     */
    public function setPm25(int $pm25): self
    {
        $this->pm25 = $pm25;

        return $this;
    }

    /**
     * Get pm25.
     *
     * @return int
     */
    public function getPm25(): int
    {
        return $this->pm25;
    }

    /**
     * Set no2.
     *
     * @param int $no2
     *
     * @return CityProbePoint
     */
    public function setNo2(int $no2): self
    {
        $this->no2 = $no2;

        return $this;
    }

    /**
     * Get no2.
     *
     * @return int
     */
    public function getNo2(): int
    {
        return $this->no2;
    }

    /**
     * Set firmwareVersion.
     *
     * @param int $firmwareVersion
     *
     * @return CityProbePoint
     */
    public function setFirmwareVersion($firmwareVersion): self
    {
        $this->firmwareVersion = $firmwareVersion;

        return $this;
    }

    /**
     * Get firmwareVersion.
     *
     * @return int
     */
    public function getFirmwareVersion()
    {
        return $this->firmwareVersion;
    }
}
