<?php
namespace ExifTools;

/**
* A class to invoke methods on exifTool software
*/
class ExifTools
{
    const IMG_PATH = "../web/files/";
    
    /**
    * Static method to extract metadata with exiftool and create a json file of it.
    * @param <string>file name with ext of the img
    * @throws specific exception if doesn't find file.
    */
    public static function generateImgMeta(string $fileName)
    {   
        if (file_exists(self::IMG_PATH . $fileName)) {
            //get file name without ext
            $imgName = preg_replace("/\.\w*/", "", $fileName);
            //we lauch the command and create the file
            $command = "exiftool " . self::IMG_PATH . $fileName . " -json -g > " . self::IMG_PATH . $imgName . ".json";
            exec($command);
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
    }

    /**
    * Static method to return metadata from it json file.
    * @param <string>file name with ext of the img
    * @return <array>image meta
    * @throws specific exception if doesn't find img or json file.
    */
    public static function getImgMeta(string $fileName) :array
    {
        if (file_exists(self::IMG_PATH . $fileName)) {
            //get the json image name:
            $jsonName = preg_replace("/\.\w*/", ".json", $fileName);

            if (file_exists(self::IMG_PATH . $jsonName)) {
                $content = file_get_contents(self::IMG_PATH . $jsonName);
                $metaArray = json_decode($content, true)[0];
            } else {
                throw new \Exception("Error, json file doesn't exist");
            }
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
        return $metaArray;
    }

    /**
    * Static method to update jsonFile and image metadata.
    * @param <array>metada array to push in the img, <string>file name with ext of the img
    * @throws specific exception if doesn't find img or json file.
    */
    public static function setImgMeta(array $meta, string $fileName)
    {
        if (file_exists(self::IMG_PATH . $fileName)) {
            //get the json image name:
            $jsonName = preg_replace("/\.\w*/", ".json", $fileName);

            if (file_exists(self::IMG_PATH . $jsonName)) {

                //pushing the change to the image (we drop the old one and push the new one instead)
                $exifRequest = '{';
                foreach ($meta as $category => $key) {
                    if (is_array($meta[$category])){
                        $exifRequest .= $category . '={';
                        foreach ($key as $key2 => $value) {
                            if (is_array($meta[$category][$key2])) {
                                $exifRequest .= $key2 . '={';
                                foreach ($value as $tag => $str) {
                                    $exifRequest .= $tag . '=' . $str . ',';
                                }
                                $exifRequest .= preg_replace('/,$/', '},', $exifRequest);
                            } else {
                                $exifRequest .= $key2 . '=' . $value . ',';
                            }                          
                        }
                        $exifRequest .= preg_replace('/,$/', '},', $exifRequest);
                    } else {
                        $exifRequest .= $category . '=' . $key . ',';
                    }
                }
                $exifRequest .= preg_replace('/,$/', '}', $exifRequest);

                $command = "exiftool -hierarchicalkeywords='" . $exifRequest . "' " . self::IMG_PATH . $fileName;
                exec($command);

                //generating the new json file
                self::generateImgMeta($fileName);
            } else {
                throw new \Exception("Error, json file doesn't exist");
            }
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
    }

}