<?php

class ProcessImage {

    private $db = null;
    function __construct()
    {
        $m = new Mongo();
        $this->db = $m->selectDB("mozaik");
    }

    public function perform()
    {
        echo "Preparing image resource {$this->args['filename']} for use...\n";

        $color = ImageUtils::getAverageColor($this->args['filename']);

        $doc = array(
            "isbn" => $this->args['isbn'],
            "colors" => ImageUtils::getMatrixForColor($color['red'],$color['green'],$color['blue'])
        );
        echo "Produced ".var_export($doc,true);

//        $this->db->selectCollection("images")->insert($doc);
//
        $this->db->getGridFS("jackets")->storeFile($this->args['filename'],$doc);

        foreach ($doc['colors'] as $key=>$val) {
            $this->upsertInverse($doc,$key);
        }

    }

    function upsertInverse($doc,$key) {
        $this->db->selectCollection("colors_$key")->update(
            array("_id"=>$doc['colors'][$key]),
            array('$addToSet'=>array("isbns"=>$doc["isbn"])),
            array("upsert"=>true)
        );
    }

}