<?php
namespace controllers;

use \F3, \Template;

class colors
{
    function image()
    {
        $data = $this->getData();
        $keys = array('highest','higher','high','medium','low','lowest');
        for($i=0;$i<count($keys);$i++)
        {
            $key = $keys[$i];
            $value = $data[$key];
            if ($value!=null && count($value)>1)
            {
                $isbn = $value[rand(0,count($value)-1)];
                header("X-Talis-Mozaik-ISBN: $isbn");
                header("X-Talis-Mozaik-Quality: $key");
                \ImageUtils::echoImage($isbn);
                die;
            }
        }
        F3::error(404,"Image not found"); //todo: serve blank image for dinner
    }

    function images()
    {
        header("Content-Type: application/json");
        echo json_encode($this->getData());
    }

    function mozaik()
    {
        $color = F3::get("PARAMS.color");
        $cellWidth = F3::get("GET.cellWidth");
        if (empty($cellWidth)) $cellWidth = 1;
        $headers = get_headers("http://mozaik/colors/$color/image");
        foreach($headers as $header)
        {
            if (preg_match('/X-Talis-Mozaik-ISBN/',$header))
            {
                $isbn = preg_replace('/X-Talis-Mozaik-ISBN: /','',$header);
                F3::reroute("http://mozaik/isbn/$isbn/mozaik?cellWidth=$cellWidth");
                die;
            }
        }
        F3::error(404,"Image not found"); //todo: serve blank image for dinner
    }

    function getData()
    {
        $color = F3::get("PARAMS.color");

        $r = ($color >> 16) & 0xFF;
        $g = ($color >> 8) & 0xFF;
        $b = $color & 0xFF;

        $matrix = \ImageUtils::getMatrixForColor($r,$g,$b);

        $m = new \Mongo();
        $db = $m->selectDB("mozaik");

        $results = array("matrix"=>$matrix);
        foreach ($matrix as $key=>$value)
        {
            $result = $db->selectCollection("colors_".$key)->findOne(array('_id'=>$value));
            $results[$key] = (isset($result['isbns'])) ? $result['isbns']:null;
        }
        return $results;
    }

}