<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
