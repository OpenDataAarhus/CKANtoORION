<?php

namespace AppBundle\HealthCheck\Asset;

use AppBundle\Entity\Point;
use Doctrine\ORM\EntityManager;
use ZendDiagnostics\Check\CheckInterface;

abstract class BaseAssetsCheck implements CheckInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    abstract public function check();

    public function getLabel(): string
    {
        return 'Check if asset data is updated';
    }

    protected function checkAssetPoint(Point $point, int $interval): bool
    {
        $interval = new \DateInterval('PT'.$interval.'S');
        $now = new \DateTime();

        return $point->getTimeInstant() > $now->sub($interval);
    }

    protected function getLatestPoint(string $pointClass): Point
    {
        $point = $this->entityManager->createQueryBuilder()
                                     ->select('p')
                                     ->from('AppBundle:'.$pointClass, 'p')
                                     ->orderBy('p.timeInstant', 'DESC')
                                     ->getQuery()
                                     ->setMaxResults(1)
                                     ->getSingleResult();

        return $point;
    }
}
