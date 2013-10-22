<?php
namespace controllers;

use \F3, \Template;

class images
{
    function get()
    {
        $isbn = F3::get("PARAMS.isbn");
        \ImageUtils::echoImage($isbn);
    }

    function metadata()
    {
        $m = new \Mongo();
        $grid = $m->selectDB("mozaik")->getGridFS("jackets");

        $isbn = F3::get("PARAMS.isbn");

        $file = $grid->findOne(array("isbn"=>$isbn));

        header("Content-Type: application/json");

        $output = array(
            "colors"=>$file->file['colors'],
            "isbn"=>$file->file['isbn']
        );
        echo json_encode($output);

    }

    function colors()
    {
        $isbn = F3::get("PARAMS.isbn");
        $cellWidth = F3::get("GET.cellWidth");
        if (empty($cellWidth)) $cellWidth = 1;
        header("Content-Type: application/json");
        echo json_encode(\ImageUtils::getColorsForIsbn($isbn,$cellWidth));
    }

    function mozaik()
    {
        $isbn = F3::get("PARAMS.isbn");
        $cellWidth = F3::get("GET.cellWidth");
        if (empty($cellWidth)) $cellWidth = 1;
        $matrix = \ImageUtils::getColorsForIsbn($isbn,$cellWidth);

        $content = '';
        for($h=0;$h<$matrix['height'];$h++)
        {
            for($w=0;$w<$matrix['width'];$w++)
            {
                $color = $matrix['colors'][$h][$w];
                $content .= "<a href='http://mozaik/colors/$color/image/mozaik?cellWidth=$cellWidth'><img src='http://mozaik/colors/$color/image' border='0' width='10' height='10'/></a>";
            }
            $content .= "<br/>";
        }
        $content .= "<br/><br/><br/><img src='http://mozaik/isbn/$isbn'/><br/>";

        header("Content-Type: text/html");
        echo "<html><body>$content</body></html>";
    }
}