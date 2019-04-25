<?php

namespace AppBundle\HealthCheck\Asset;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class RealTimeTrafficCheck extends BaseAssetsCheck
{
    public function check()
    {
        $point = $this->getLatestPoint('RealTimeTrafficPoint');

        if ($this->checkAssetPoint($point, 600)) {
            return new Success('Point is newer than interval');
        }

        return new Failure('Point is to old ['.$point->getTimeInstant()->format('c').']');
    }

    public function getLabel(): string
    {
        return 'RealTimeTraffic update status';
    }
}
