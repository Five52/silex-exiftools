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
            $command = "exiftool " . self::IMG_PATH . $fileName . " -G -json > " . self::IMG_PATH . $imgName .  ".json ";
            exec($command);
            //we keep a copy of the original metadata
            copy(self::IMG_PATH . $imgName . ".json", self::IMG_PATH . $imgName . ".json.original");
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
                $json = json_decode($content, true)[0];
                //modelize array:
                $metaArray = [];
                foreach ($json as $key => $value) {
                    $str = explode(':', $key);
                    if (array_key_exists(1, $str)) {
                        $metaArray[$str[0]][$str[1]] = $value;
                    } else {
                        $metaArray[$key] = $value;
                    }
                    
                }
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

                    //normalize array for json parsing:
                $metaArray = [];
                foreach ($meta as $category => $key) {
                    if (is_array($meta[$category])) {
                        foreach ($key as $tag => $value) {
                            $metaArray[$category . ":" . $tag] = $value;
                        }
                    } else {
                        $metaArray[$category] = $key;
                    }
                }

                    //convert array in json and update the file
                $jsonArray = json_encode($metaArray);
                    //we keep a copy of the metadata before changing it
                copy(self::IMG_PATH . $jsonName, self::IMG_PATH . $jsonName . ".old");
                $file = fopen(self::IMG_PATH . $jsonName, 'w');
                fwrite($file, $jsonArray);
                fclose($file);

                //update metada of the image:
                $command = "exiftool -j=" . self::IMG_PATH . $jsonName . " -G -m -overwrite_original" . self::IMG_PATH . $fileName;
                exec($command);

            } else {
                throw new \Exception("Error, json file doesn't exist");
            }
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
    }

}