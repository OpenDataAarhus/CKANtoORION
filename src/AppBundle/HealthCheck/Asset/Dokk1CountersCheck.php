<?php

namespace AppBundle\HealthCheck\Asset;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class Dokk1CountersCheck extends BaseAssetsCheck
{
    public function check()
    {
        $point = $this->getLatestPoint('Dokk1CountersPoint');

        if ($this->checkAssetPoint($point, 3600)) {
            return new Success('Point is newer than interval');
        }

        return new Failure('Point is to old ['.$point->getTimeInstant()->format('c').']');
    }

    public function getLabel(): string
    {
        return 'Dokk1Counters update status';
    }
}
