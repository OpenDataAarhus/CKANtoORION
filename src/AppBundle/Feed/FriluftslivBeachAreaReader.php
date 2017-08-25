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

class FriluftslivBeachAreaReader extends BaseFriluftslivGeoJsonReader
{
    protected $feed_path = '/dataset/f93c5e24-6a6c-4a6e-9a95-d680b0fd7354/resource/bd4ef357-d6bb-4be9-ac06-60548f1acde6/download/NaturarealervedStrandenWGS84.json';

    protected $id_string = 'beacharea';
    protected $type = 'beacharea';
    protected $origin_value = 'Beach areas from Friluftliv Aarhus';
    protected $origin_url = 'https://www.odaa.dk/dataset/naturarealer-ved-strande';

}