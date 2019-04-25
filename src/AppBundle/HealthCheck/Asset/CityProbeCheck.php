<?php

namespace AppBundle\HealthCheck\Asset;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class CityProbeCheck extends BaseAssetsCheck
{
    public function check()
    {
        $point = $this->getLatestPoint('CityProbePoint');

        if ($this->checkAssetPoint($point, 1200)) {
            return new Success('Point is newer than interval');
        }

        return new Failure('Point is to old ['.$point->getTimeInstant()->format('c').']');
    }

    public function getLabel(): string
    {
        return 'CityProbe data update status';
    }
}
