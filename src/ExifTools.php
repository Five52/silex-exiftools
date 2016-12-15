<?php
namespace ExifTools;

/**
 * A class to invoke methods on exifTool software
 */
class ExifTools
{

    const META_PATH = "../web/files/metadata/";
    /**
     * Static method to extract metadata with exiftool and create a json file of it.
     * @param <Image>instance of the target image
     * @throws specific exception if doesn't find file.
     */
    public static function generateImgMeta(Image $img)
    {
        if (file_exists($img->getPath())) {
            //we lauch the command and create the file
            $command = "exiftool " . $img->getPath() . " -G -json > " . self::META_PATH . $img->getId() .  ".json ";
            exec($command);
            //we keep a copy of the original metadata
            copy(self::META_PATH . $img->getId() . ".json", self::META_PATH . $img->getId() . ".json.original");
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
    }

    /**
     * Static method to return metadata from it json file.
     * @param <Image>instance of the target image
     * @return <array>image meta
     * @throws specific exception if doesn't find img file
     */
    public static function getImgMeta(Image $img)
    {
        if (file_exists($img->getPath())) {
            //if the json file doesn't exist we generate it
            $jsonPath = self::META_PATH . $img->getId() . ".json";
            if (!file_exists($jsonPath)) {
                self::generateImgMeta($img);
            }

            $content = file_get_contents($jsonPath);
            $metaArray = json_decode($content, true)[0];
            // //modelize array:
            // $metaArray = [];
            // foreach ($json as $key => $value) {
            //     $str = explode(':', $key);
            //     if (array_key_exists(1, $str)) {
            //         $metaArray[$str[0]][$str[1]] = $value;
            //     } else {
            //         $metaArray[$key] = $value;
            //     }
            // }
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
        return $metaArray;
    }

    /**
     * Static method to update jsonFile and image metadata.
     * @param <array>metada array to push in the img, <Image>instance of the target image
     * @throws specific exception if doesn't find img or json file.
     */
    public static function setImgMeta(array $meta, Image $img)
    {
        if (file_exists($img->getPath())) {
            //get the json image name:
            $jsonPath = self::META_PATH . $img->getId() . ".json";

            if (file_exists($jsonPath)) {

                //pushing the change to the image (we drop the old one and push the new one instead)

                //     //normalize array for json parsing:
                // $metaArray = [];
                // foreach ($meta as $category => $key) {
                //     if (is_array($meta[$category])) {
                //         foreach ($key as $tag => $value) {
                //             $metaArray[$category . ":" . $tag] = $value;
                //         }
                //     } else {
                //         $metaArray[$category] = $key;
                //     }
                // }

                    //convert array in json and update the file
                $jsonArray = json_encode($meta);
                    //we keep a copy of the metadata before changing it
                copy($jsonPath, $jsonPath . ".old");
                $file = fopen($jsonPath, 'w');
                fwrite($file, $jsonArray);
                fclose($file);

                //update metada of the image:
                $command = "exiftool -j=" . $jsonPath . " -G -m -overwrite_original " . $img->getPath();
                exec($command);

            } else {
                throw new \Exception("Error, json file doesn't exist");
            }
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
    }

    /**
     * Static method to restaure original metadata of the image.
     * @param <Image>instance of the target image
     * @throws specific exception if doesn't find img or json file.
     */
    public static function resetOriginalMeta(Image $img)
    {
        if (file_exists($img->getPath())) {
            //get the json image name:
            $jsonOriginalPath = self::META_PATH . $img->getId() . ".json.original";
            $jsonActualPath = self::META_PATH . $img->getId() . ".json";

            if (file_exists($jsonOriginalPath)) {
                //update metada of the image:
                $command = "exiftool -j=" . $jsonOriginalPath . " -G -m -overwrite_original " . $img->getPath();
                copy($jsonOriginalPath, $jsonActualPath);
                exec($command);
            } else {
                throw new \Exception("Error, json file doesn't exist");
            }
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
    }

    /**
     * Static method to restaure last metadata of the image.
     * @param <Image>instance of the target image
     * @throws specific exception if doesn't find img or json file.
     */
    public static function resetLastMeta(Image $img)
    {
        if (file_exists($img->getPath())) {
            //get the json image name:
            $jsonOldPath = self::META_PATH . $img->getId() . ".json.old";
            $jsonActualPath = self::META_PATH . $img->getId() . ".json";

            if (file_exists($jsonOldPath)) {
                //update metada of the image:
                $command = "exiftool -j=" . $jsonOldPath . " -G -m -overwrite_original " . $img->getPath();
                copy($jsonOldPath, $jsonActualPath);
                exec($command);
            } else {
                throw new \Exception("Error, json file doesn't exist");
            }
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
    }

    /**
     * Static method to generate xmp file and return download link.
     * @param <Image>instance of the target image
     * @return <string>link of file xmp sidecar metadata link
     * @throws specific exception if doesn't find img or xmp file.
     */
    public static function generateXmpLink(Image $img)
    {
        if (file_exists($img->getPath())) {
            //get the xmp image name:
            $xmpPath = self::META_PATH . $img->getId() . ".xmp";

            //we generate/regenerate the file:
            $command = "exiftool -TagsFromFile " . $img->getPath() . " -overwrite_original " . $xmpPath;
            exec($command);

            //returning the xmp file link:
            return $xmpPath;
        } else {
            throw new \Exception("Error, image file doesn't exist");
        }
    }

    /**
     * Static method to delete metadata files of the image.
     * @param <Image>instance of the target image
     */
    public static function deleteMetaFiles(Image $image)
    {
        $filesExtensions = ['json', 'json.original', 'json.old', 'xmp'];
        foreach ($filesExtensions as $extension) {
            $file = self::META_PATH . $image->getId() . '.' . $extension;
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
