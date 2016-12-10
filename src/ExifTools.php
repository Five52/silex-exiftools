<?php
namespace ExifTools;
/**
* A class to invoke methods on exifTool software
*/
class ExifTools
{
    
    /**
    * Static method to extract metadata with exiftool and create a json file of it.
    * @return <bool>
    */
    public static function getImgDetails(string $fileName) :bool
    {   
        $imgName = preg_replace("/\.\w*/", "", $fileName);
        $path = "../web/files/";

        //we first check if the file exist:
        exec("[ -f " . $path . $fileName . " ] && echo 'true' || ''", $output);
        if ($output) {
            //we lauch the command and create the file
            $command = "exiftool " . $path . $fileName . " -json -g > " . $path . $imgName . ".json";
            exec($command);
            return true;
        } else {
            return false;
        }
    }

}