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

class FriluftslivParksReader extends BaseFriluftslivPolygonReader
{
  protected $feed_path = '/dataset/d19833aa-7efc-4461-ad4e-43fecefc573f/resource/7c5436c3-240b-4d1e-bca0-3a0b76c6f4fa/download/ParkerWGS84.json';

  protected $id_string = 'park';
  protected $type = 'parks';
  protected $origin_value = 'Parks from Friluftliv Aarhus';
  protected $origin_url = 'https://www.odaa.dk/dataset/skove-og-parker-i-aarhus';

}