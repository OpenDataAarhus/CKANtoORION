<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35
 */

namespace AppBundle\Feed;

use Exception;
use DateTime;
use DateTimeZone;
use stdClass;

class FriluftslivForestSmallReader extends BaseFriluftslivGeoJsonReader
{
    protected $feed_path = '/dataset/d19833aa-7efc-4461-ad4e-43fecefc573f/resource/5da35433-5f77-4305-afd1-c1d8ca80b77a/download/SkovemindreWGS84.json';

    protected $id_string = 'forest';
    protected $type = 'forests';
    protected $origin_value = 'Forests from Friluftliv Aarhus';
    protected $origin_url = 'https://www.odaa.dk/dataset/skove-og-parker-i-aarhus';

}