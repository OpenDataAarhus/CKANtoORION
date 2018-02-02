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

class FriluftslivHikingTrailsReader extends BaseFriluftslivGeoJsonReader
{
    protected $feed_path = '/dataset/04a88935-fd69-4d17-a3d5-42b815d23da3/resource/a873468f-4213-49f4-ac22-f5f064ca98b6/download/VandreruterWGS84.json';

    protected $id_string = 'hikingtrail';
    protected $type = 'hikingtrails';
    protected $origin_value = 'Hiking Trails from Friluftliv Aarhus';
    protected $origin_url = 'https://www.odaa.dk/dataset/vandreruter';

}