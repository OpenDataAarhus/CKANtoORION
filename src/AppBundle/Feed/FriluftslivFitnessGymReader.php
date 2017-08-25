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

class FriluftslivFitnessGymReader extends BaseFriluftslivPointReader
{
    protected $feed_path = '/dataset/ca1b668e-71d6-4890-b1d2-b222c89ea762/resource/194e7fad-907c-4271-9a55-55fe8f296104/download/FitnessidetfriWGS84.json';

    protected $id_string = 'fitnessspots';
    protected $type = 'fitnessspot';
    protected $origin_value = 'Public Firepits from Friluftliv Aarhus';
    protected $origin_url = 'https://www.odaa.dk/dataset/fitness-i-det-fri-aarhus';

}