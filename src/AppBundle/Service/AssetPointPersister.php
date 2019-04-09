<?php

namespace AppBundle\Service;

use AppBundle\Entity\Asset;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AssetPointPersister
{
    private $em;
    private $propertyAccessor;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function persistPointsByTimestamp($assets, $className): void
    {
        $assetClassName = '\AppBundle\Entity\\'.$className.'Asset';
        $pointClassName = '\AppBundle\Entity\\'.$className.'Point';

        $lastPointByAsset = $this->getLatestPoints($pointClassName);

        foreach ($assets as $assetData) {
            $asset = $this->em->getRepository($assetClassName)->findOneBy(['identifier' => $assetData['id']]);
            $point = new $pointClassName();

            if (!$asset) {
                $asset = new $assetClassName();
                $this->em->persist($asset);
            }

            $this->setAssetData($asset, $assetData);
            $lastTime = (!$lastPointByAsset || !$asset->getId()) ? null : DateTime::createFromFormat('Y-m-d H:i:s', $lastPointByAsset[$asset->getId()], new \DateTimeZone('UTC'));
            $newTime = DateTime::createFromFormat('Y-m-d\TH:i:s.uT', $assetData['TimeInstant']['value']);

            if ($lastTime < $newTime) {
                $pointsData = $this->getPointsData($assetData);
                $this->setPointsData($point, $asset, $pointsData, $newTime);
                $this->em->persist($point);
            }
        }

        $this->em->flush();
    }

    public function persistPointsById(&$assets, $className): array
    {
        $pointClassName = '\AppBundle\Entity\\'.$className.'Point';

        $assetEntities = [];

        foreach ($assets as &$assetData) {
            if (!array_key_exists($assetData['id'], $assetEntities)) {
                $assetEntities[$assetData['id']] = $this->getAssetEntity($assetData['id'], $className);
            }
            $this->setAssetData($assetEntities[$assetData['id']], $assetData);

            $point = $this->em->getRepository($pointClassName)->findOneBy(['id' => $assetData['pointId']]);
            if (!$point) {
                $point = new $pointClassName();
                $this->propertyAccessor->setValue($point, 'id', $assetData['pointId']);
            }

            $pointsData = $this->getPointsData($assetData);
            $pointTime = DateTime::createFromFormat('Y-m-d\TH:i:s.uT', $assetData['TimeInstant']['value']);
            $this->setPointsData($point, $assetEntities[$assetData['id']], $pointsData, $pointTime);
            $this->em->persist($point);

            unset($assetData['pointId']);
        }

        $this->em->flush();

        return $assets;
    }

    private function getAssetEntity(string $id, string $className): Asset
    {
        $assetClassName = '\AppBundle\Entity\\'.$className.'Asset';

        $asset = $this->em->getRepository($assetClassName)->findOneBy(['identifier' => $id]);
        if (!$asset) {
            $asset = new $assetClassName();
            $this->em->persist($asset);
        }

        return $asset;
    }

    private function setAssetData($asset, $assetData)
    {
        $asset->setIdentifier($assetData['id']);

        if ('null' !== $assetData['location']['value']) {
            $location = explode(',', $assetData['location']['value']);
            $asset->setLatitude($location[0]);
            $asset->setLongitude($location[1]);
        }

        $asset->setName($assetData['origin']['value']);
        $asset->setUrl($assetData['origin']['metadata']['urls']['value']);
        $asset->setType($assetData['type']);
    }

    private function getPointsData($assetData)
    {
        unset($assetData['id'], $assetData['location'], $assetData['origin'], $assetData['type']);

        return $assetData;
    }

    private function setPointsData($point, $asset, $pointsData, $timeInstant)
    {
        $this->propertyAccessor->setValue($point, 'asset', $asset);
        $this->propertyAccessor->setValue($point, 'TimeInstant', $timeInstant);

        foreach ($pointsData as $key => $value) {
            switch ($key) {
        case 'TimeInstant':
          break;
        case 'pointId':
          break;
        default:
          if (array_key_exists('value', $value)) {
              $key = str_replace(':', '_', $key);
              $key = str_replace('.', '', $key);
              if ($this->propertyAccessor->isWritable($point, $key)) {
                  $this->propertyAccessor->setValue($point, $key, $value['value']);
              }
          }
      }
        }
    }

    private function getLatestPoints($pointClassName)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('IDENTITY(p.asset) id, MAX(p.timeInstant) as timeInstant')
      ->from($pointClassName, 'p')
      ->groupBy('id');

        $result = $qb->getQuery()->execute();

        return array_column($result, 'timeInstant', 'id');
    }
}
