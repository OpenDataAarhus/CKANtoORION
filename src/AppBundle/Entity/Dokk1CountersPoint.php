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
class Dokk1CountersPoint extends Point
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
     * @ORM\ManyToOne(targetEntity="Dokk1CountersAsset", inversedBy="points")
     */
    private $asset;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $visitorsIn;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $visitorsOut;

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
    public function getVisitorsIn()
    {
        return $this->visitorsIn;
    }

    /**
     * @param int $visitorsIn
     */
    public function setVisitorsIn($visitorsIn)
    {
        $this->visitorsIn = $visitorsIn;
    }

    /**
     * @return int
     */
    public function getVisitorsOut()
    {
        return $this->visitorsOut;
    }

    /**
     * @param int $visitorsOut
     */
    public function setVisitorsOut($visitorsOut)
    {
        $this->visitorsOut = $visitorsOut;
    }
}
