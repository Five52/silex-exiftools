<?php
namespace ExifTools;

class Image
{
    const IMG_PATH = "../web/files/";

    protected $id;
    protected $extension;
    protected $latestMeta;

    public function __construct($id, $extension)
    {
        $this->id = $id;
        $this->extension = $extension;
        $this->latestMeta = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function getPath()
    {
        return self::IMG_PATH . $this->id . '.' . $this->extension;
    }

    public function getLatestMeta()
    {
        if ($this->latestMeta === null) {
            $this->latestMeta = ExifTools::getImgMeta($this);
        }
        return $this->latestMeta;
    }

}
