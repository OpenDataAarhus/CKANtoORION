<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35.
 */

namespace AppBundle\Feed;

class FriluftslivForestReader extends BaseFriluftslivGeoJsonReader
{
    protected $feed_path = '/dataset/d19833aa-7efc-4461-ad4e-43fecefc573f/resource/e4bd8fe0-8cb8-4756-ba4c-d7edbf944917/download/skovestorewgs84.json';

    protected $id_string = 'forest';
    protected $type = 'forests';
    protected $origin_value = 'Forests from Friluftliv Aarhus';
    protected $origin_url = 'https://portal.opendata.dk/dataset/skove-og-parker-i-aarhus';
}
