<?php

namespace AppBundle\Service;

use DateTime;
use AppBundle\Entity\Dokk1CountersAsset;
use AppBundle\Entity\Dokk1CountersPoint;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;


class AssetPointPersister
{
  private $em;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  public function persistPoints($assets, $className)
  {

    $assetClassName = '\AppBundle\Entity\\' . $className . 'Asset';
    $pointClassName = '\AppBundle\Entity\\' .$className . 'Point';

    $lastPointByAsset = $this->getLatestPoints($pointClassName);

    foreach ($assets as $assetData) {
      $asset = $this->em->getRepository($assetClassName)->findOneBy(array('identifier' => $assetData['id']));
      $point = new $pointClassName;

      if(!$asset) {
        $asset = new $assetClassName;
        $this->em->persist($asset);
      }

      $this->setAssetData($asset, $assetData);
      $lastTime = (!$lastPointByAsset || !$asset->getId()) ? null : DateTime::createFromFormat( 'Y-m-d H:i:s', $lastPointByAsset[$asset->getId()], new \DateTimeZone('UTC'));
      $newTime = DateTime::createFromFormat( 'Y-m-d\TH:i:s.uT', $assetData['TimeInstant']['value']);

      if($lastTime < $newTime) {
        $pointsData = $this->getPointsData($assetData);
        $this->setPointsData($point, $asset, $pointsData, $newTime);
        $this->em->persist($point);
      }
    }

    $this->em->flush();
  }

  private function setAssetData($asset, $assetData) {
    $asset->setIdentifier($assetData['id']);

    $location = explode(',', $assetData['location']['value']);
    $asset->setLatitude($location[0]);
    $asset->setLongitude($location[1]);

    $asset->setName($assetData['origin']['value']);
    $asset->setUrl($assetData['origin']['metadata']['urls']['value']);
    $asset->setType($assetData['type']);
  }

  private function getPointsData($assetData) {
    unset($assetData['id'], $assetData['location'], $assetData['origin'], $assetData['type']);

    return $assetData;
  }

  private function setPointsData($point, $asset, $pointsData, $timeInstant) {
    $propertyAccessor = PropertyAccess::createPropertyAccessor();
    $propertyAccessor->setValue($point, 'asset', $asset);
    $propertyAccessor->setValue($point, 'TimeInstant', $timeInstant);

    foreach ($pointsData as $key => $value) {
      switch ($key) {
        case 'TimeInstant':
          break;
        default:
          if(array_key_exists('value', $value)) {
            $k = str_replace(':', '_', $key);
            if($propertyAccessor->isWritable($point, $k)) {
              $propertyAccessor->setValue($point, $k, $value['value']);
            }
          }
      }
    }
  }

  private function getLatestPoints($pointClassName) {
    $qb = $this->em->createQueryBuilder();

    $qb->select('IDENTITY(p.asset) id, MAX(p.timeInstant) as timeInstant')
      ->from($pointClassName, 'p')
      ->groupBy('id');

    $result = $qb->getQuery()->execute();

    return array_column($result, 'timeInstant', 'id');
  }
}
