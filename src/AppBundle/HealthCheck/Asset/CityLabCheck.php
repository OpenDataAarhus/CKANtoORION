<?php

namespace AppBundle\HealthCheck\Asset;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class CityLabCheck extends BaseAssetsCheck
{
    public function check()
    {
        $point = $this->getLatestPoint('CityLabPoint');

        if ($this->checkAssetPoint($point, 600)) {
            return new Success('Point is newer than interval');
        }

        return new Failure('Point is to old ['.$point->getTimeInstant()->format('c').']');
    }

    public function getLabel(): string
    {
        return 'CityLab data update status';
    }
}
