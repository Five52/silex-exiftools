<?php
namespace ExifTools;
/**
* A class to invoke methods on exifTool software
*/
class ExifTools
{
    const IMG_PATH = "../web/files/";

    public function fileExist(string $fileName) :bool
    {
        exec("[ -f " . self::IMG_PATH . $fileName . " ] && echo 'true' || ''", $output);
        if ($output) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
    * Static method to extract metadata with exiftool and create a json file of it.
    * @param <string>file name with ext of the img
    * @return <bool>
    */
    public static function generateImgMeta(string $fileName) :bool
    {   
        $fileExist = self::fileExist($fileName);
        
        if ($fileExist) {
            //get file name without ext
            $imgName = preg_replace("/\.\w*/", "", $fileName);
            //we lauch the command and create the file
            $command = "exiftool " . self::IMG_PATH . $fileName . " -json -g > " . self::IMG_PATH . $imgName . ".json";
            exec($command);
            return true;
        } else {
            return false;
        }
    }

    /**
    * Static method to return metadata from it json file.
    * @param <string>file name with ext of the img
    * @return <array>image meta
    */
    public static function getImgMeta(string $fileName) :array
    {
        $fileExist = self::fileExist($fileName);
        
        if ($fileExist) {
            //get the json image name:
            $jsonName = preg_replace("/\.\w*/", ".json", $fileName);
            $jsonExist = self::fileExist($jsonName);

            if ($jsonExist) {
                $content = file_get_contents(self::IMG_PATH . $jsonName);
                $metaArray = json_decode($content, true)[0];
                var_dump($metaArray);
                return $metaArray;
            } else {
                //the json file doesn't exist
            }
        } else {
            //the img file doesn't exist
        }
    }

    /**
    * Static method to extract metadata with exiftool and create a json file of it.
    * @return <bool>
    */
    public static function setImgMeta(string $fileName) :bool
    {

    }

}