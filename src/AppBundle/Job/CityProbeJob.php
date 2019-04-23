<?php

namespace AppBundle\Job;

class CityProbeJob extends BaseJob
{
    protected $interval = 15; // 300; // 5 * 60

    public function run($args)
    {
        parent::run($args);

        $feed = $this->getContainer()->get('app.feed_reader_factory')->getFeedReader('city_probe');
        $assets = $feed->normalizeForOrganicity();

        $assets = $this->pointsPersister->persistPointsById($assets, 'CityProbe');

        $assets = $this->removeDuplicates($assets);
        $assets = $this->removeNullGeoPoints($assets);
        $this->spawnBatchJob($assets);
    }

    private function removeNullGeoPoints(array &$assets): array
    {
        return array_filter($assets, static function ($asset) {
            return 'null' !== $asset['location']['value'];
        });
    }
}
