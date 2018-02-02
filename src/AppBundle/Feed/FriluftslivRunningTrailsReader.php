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

class FriluftslivRunningTrailsReader extends BaseFriluftslivGeoJsonReader
{
    protected $feed_path = '/dataset/1fa14692-c917-479d-b132-7b1979b20ea8/resource/7e8ca6cd-8454-49c5-adc2-a59ce511a7f4/download/Loeberuter2011WGS84.json';

    protected $id_string = 'runningtrail';
    protected $type = 'runningtrails';
    protected $origin_value = 'Running Trails from Friluftliv Aarhus';
    protected $origin_url = 'https://www.odaa.dk/dataset/loberuter';

}