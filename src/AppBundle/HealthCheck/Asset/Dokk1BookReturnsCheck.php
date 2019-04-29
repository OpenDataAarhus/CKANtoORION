<?php

namespace AppBundle\HealthCheck\Asset;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class Dokk1BookReturnsCheck extends BaseAssetsCheck
{
    public function check()
    {
        $point = $this->getLatestPoint('Dokk1BookReturnsPoint');

        if ($this->checkAssetPoint($point, 3600)) {
            return new Success('Point is newer than interval');
        }

        return new Failure('Dokk1BookReturns point is to old ['.$point->getTimeInstant()->format('c').']');
    }

    public function getLabel(): string
    {
        return 'Dokk1BookReturns update status';
    }
}
