<?php

if (count($argv) < 2)
{
    echo "\nUsage: php processImages.php <folder>";
    die;
}

require '../src/config.inc.php';

$imageCount = 0;

$dir = @$argv[1];
$dh  = opendir($dir);
while (false !== ($filename = readdir($dh)))
{
    if (substr($filename,-4) == '.img')
    {
        echo "\n\nFound image folder $filename\n";
        $ifh = opendir($dir.$filename);
        while (false !== ($imageFilename = readdir($ifh)))
        {
            if (substr($imageFilename,-4) == '.jpg')
            {
                $imageCount++;
                $isbn = substr($imageFilename,0,-4);
                $fullPath = $dir.$filename.'/'.$imageFilename;
                echo "   Found ISBN $isbn, filename: $fullPath\n";

                $args = array(
                    'filename' => $fullPath,
                    'isbn' => $isbn
                );
                Resque::enqueue('images', 'ProcessImage', $args);
            }
            if (($imageCount % 10000) == 0 && $imageCount>0) {
                echo "(Processed $imageCount files)\n";
            }
        }
    }
}
echo "\n\nFINISHED, Processed $imageCount files\n\n";