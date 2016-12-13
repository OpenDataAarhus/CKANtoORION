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

class FriluftslivDogWalkingAreaReader extends BaseFriluftslivPolygonReader
{
  protected $feed_path = '/dataset/062e8018-53ba-4368-b357-dfeeda068ef7/resource/e220e1a7-7392-483f-9db5-f3eee91b4e8f/download/HundeskoveWGS84.json';

  protected $id_string = 'dogwalkingarea';
  protected $type = 'dogwalkingarea';
  protected $origin_value = 'Dog walking areas from Friluftliv Aarhus';
  protected $origin_url = 'https://www.odaa.dk/dataset/hundeskove-i-aarhus';

}