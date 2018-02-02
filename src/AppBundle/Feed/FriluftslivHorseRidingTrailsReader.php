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

class FriluftslivHorseRidingTrailsReader extends BaseFriluftslivGeoJsonReader
{
    protected $feed_path = '/dataset/2eeec6af-09e1-4046-aea5-f4056f067ac7/resource/b2c04be4-4426-4c9c-8df7-9d5f2b2fb7b6/download/RideruterWGS84.json';

    protected $id_string = 'horseridingtrail';
    protected $type = 'horseridingtrails';
    protected $origin_value = 'Horse Riding Trails from Friluftliv Aarhus';
    protected $origin_url = 'https://www.odaa.dk/dataset/rideruter';

}