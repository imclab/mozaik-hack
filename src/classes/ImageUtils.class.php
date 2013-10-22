<?php

class ImageUtils {
    static function getMatrixForColor($red,$green,$blue)
    {
        return array (
            "highest" => dechex($red).':'.dechex($green).':'.dechex($blue),
            "higher" => dechex(round($red/2)).':'.dechex(round($green/2)).':'.dechex(round($blue/2)),
            "high" => dechex(round($red/4)).':'.dechex(round($green/4)).':'.dechex(round($blue/4)),
            "medium" => dechex(round($red/8)).':'.dechex(round($green/8)).':'.dechex(round($blue/8)),
            "low" => dechex(round($red/16)).':'.dechex(round($green/16)).':'.dechex(round($blue/16)),
            "lowest" => dechex(round($red/32)).':'.dechex(round($green/32)).':'.dechex(round($blue/32))
        );
    }

    static function getAverageColor($filename)
    {
        // load the image
        $image = imagecreatefromjpeg($filename);

        $width = imagesx($image);
        $height = imagesy($image);
        $pixel = imagecreatetruecolor(1, 1);
        imagecopyresampled($pixel, $image, 0, 0, 0, 0, 1, 1, $width, $height);
        $rgb = imagecolorat($pixel, 0, 0);

        return imagecolorsforindex($pixel, $rgb);
    }

    static function getColorsForImage($stream,$cellWidth=1)
    {
        // load the image
        $data = '';
        while (!feof($stream)) {
            $data .= fread($stream, 51200);
        }
        if ($cellWidth>1)
        {
            // resample image
            $original = imagecreatefromstring($data);
            $originalW = imagesx($original);
            $originalH = imagesy($original);
            $image = imagecreatetruecolor($originalW,$originalH);
            imagecopyresampled($image,$original, 0 , 0, 0, 0, $originalW/$cellWidth, $originalH/$cellWidth, $originalW, $originalH);
        }
        else {
            $image = imagecreatefromstring($data);
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $result = array();
        for ($h=0;$h<$height;$h++)
        {
            for ($w=0;$w<$width;$w++)
            {
                $rgb = imagecolorat($image, $w, $h);
                $result[$h][$w] = $rgb;
            }
        }
        return array("width"=>$width/$cellWidth,"height"=>$height/$cellWidth,"colors"=>$result);
    }

    static function echoImage($isbn)
    {
        $m = new \Mongo();
        $grid = $m->selectDB("mozaik")->getGridFS("jackets");

        $file = $grid->findOne(array("isbn"=>$isbn));

        header("Content-Type: image/jpg");
        $stream = $file->getResource();

        while (!feof($stream)) {
            echo fread($stream, 51200);
        }
    }

    static function getColorsForIsbn($isbn,$cellWidth=1)
    {
        $m = new \Mongo();
        $grid = $m->selectDB("mozaik")->getGridFS("jackets");

        $file = $grid->findOne(array("isbn"=>$isbn));

        header("Content-Type: application/json");
        return \ImageUtils::getColorsForImage($file->getResource(),$cellWidth);
    }
}