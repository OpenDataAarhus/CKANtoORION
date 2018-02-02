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

class FriluftslivShelterReader extends BaseFriluftslivPointReader {

	protected $feed_path = '/dataset/dc7ca516-90a3-4bea-8ceb-4bc58407d8bc/resource/4757ccaa-247f-4016-8a2b-9ca41f569db1/download/shelterswgs84.json';

	protected $id_string = 'shelters';
	protected $type = 'shelter';
	protected $origin_value = 'Shelters from Friluftliv Aarhus';
	protected $origin_url = 'https://portal.opendata.dk/dataset/shelters-i-aarhus';

}