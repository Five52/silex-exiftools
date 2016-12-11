<?php
namespace ExifTools;

class Image
{
    const IMG_PATH = "../web/files/";

    protected $id;
    protected $extension;
    protected $latestMeta;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    public function getName()
    {
        return $this->id . '.' . $this->extension;
    }

    public function getPath()
    {
        return self::IMG_PATH . $this->getName();
    }

    public function getLatestMeta()
    {
        if ($this->latestMeta === null) {
            $this->latestMeta = ExifTools::getImgMeta($this);
        }
        return $this->latestMeta;
    }

}
