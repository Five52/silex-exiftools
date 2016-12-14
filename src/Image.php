<?php
namespace ExifTools;

class Image
{
    const IMG_PATH = "../web/files/img";

    protected $id;
    protected $extension;
    protected $latestMeta;
    protected $basicMeta;

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
        return self::IMG_PATH . '/' . $this->getName();
    }

    public function getLatestMeta()
    {
        if ($this->latestMeta === null) {
            $this->latestMeta = ExifTools::getImgMeta($this);
        }
        return $this->latestMeta;
    }

    public function getBasicMeta()
    {   
        if ($this->basicMeta === null) {
            $meta = self::getLatestMeta();
            $basicMeta = [];

            //Data title:
            if (array_key_exists('XMP:Title', $meta)) {
                $basicMeta['title'] = $meta['XMP:Title'];
            } elseif (array_key_exists('XMP:Headline', $meta)) {
                $basicMeta['title'] = $meta['XMP:Headline'];
            } elseif (array_key_exists('IPTC:Headline', $meta)) {
                $basicMeta['title'] = $meta['IPTC:Headline'];
            } elseif (array_key_exists('IPTC:ObjectName', $meta)) {
                $basicMeta['title'] = $meta['IPTC:ObjectName'];
            } else {
                $basicMeta['title'] = $meta['File:FileName'];
            }

            //Data description:
            if (array_key_exists('XMP:Description', $meta)) {
                $basicMeta['description'] = $meta['XMP:Description'];
            } elseif (array_key_exists('IPTC:Caption-Abstract', $meta)) {
                $basicMeta['description'] = $meta['IPTC:Caption-Abstract'];
            } else {
                $basicMeta['description'] = "No description provided";
            }

            //Data author:
            if (array_key_exists('XMP:Creator', $meta)) {
                $basicMeta['author'] = $meta['XMP:Creator'];
            } elseif (array_key_exists('XMP:Credit', $meta)) {
                $basicMeta['author'] = $meta['XMP:Credit'];
            } elseif (array_key_exists('IPTC:By-line', $meta)) {
                $basicMeta['author'] = $meta['IPTC:By-line'];
            } elseif (array_key_exists('IPTC:Credit', $meta)) {
                $basicMeta['author'] = $meta['IPTC:Credit'];
            } else {
                $basicMeta['author'] = "Author unknown";
            }
            $this->basicMeta = $basicMeta;
        }
        return $this->basicMeta;
    }

    public function getXmpPath()
    {
        return ExifTools::generateXmpLink($this);
    }

}
