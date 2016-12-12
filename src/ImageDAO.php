<?php
namespace ExifTools;

class ImageDAO
{
    public static function getAll()
    {
        $files = glob(Image::IMG_PATH . '/*');
        $images = [];

        foreach ($files as $file) {
            $infos = explode('.', $file);
            $image = new Image();
            $image->setId($infos[0])->setExtension($infos[1]);
            $images[] = $image;
        }

        return $images;
    }

    public static function get($id, $extension)
    {
        $image = new Image();
        $image->setId($id)->setExtension($extension);
        return $image;
    }
}
