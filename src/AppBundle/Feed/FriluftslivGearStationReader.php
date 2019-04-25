<?php
/**
 * Created by PhpStorm.
 * User: turegjorup
 * Date: 24/08/16
 * Time: 12:35.
 */

namespace AppBundle\Feed;

class FriluftslivGearStationReader extends BaseFriluftslivPointReader
{
    protected $feed_path = '/dataset/cb4df027-acb2-4cbe-928a-73e58ae6caf3/resource/dbe736be-5851-4281-96c8-308602ed4250/download/grejbaserwgs84.json';

    protected $id_string = 'gearstations';
    protected $type = 'gearstation';
    protected $origin_value = 'Gearstations from Friluftliv Aarhus';
    protected $origin_url = 'https://portal.opendata.dk/dataset/grejbaser-ved-aarhus-kommune';
}
