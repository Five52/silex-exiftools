<?php
namespace ExifTools;

/**
 * Class representing an image
 */
class Image
{
    const IMG_PATH = "../web/files/img";
    /* Const needed for generating facebook graph and twitter card (need absolute url) */
    const URI = "https://dev-21101555.users.info.unicaen.fr";
    const IMG_ABSOLUTE_PATH = "https://dev-21101555.users.info.unicaen.fr/M2-DNR2i/silex-exiftools/web/files/img";

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

    public function getAbsoluteImgPath()
    {
        return self::IMG_ABSOLUTE_PATH . '/' . $this->getName();
    }

    public function getUri()
    {
        return self::URI;
    }

    /**
     * Return every meta category existing for an image excepted ExifTool and sourceFile.
     * @return <array> containing category name
     */
    public function getMetaCategory()
    {
        $categories = [];
        $meta = self::getLatestMeta();
        foreach ($meta as $key => $value) {
            $category = explode(':', $key)[0];
            if ($category != "SourceFile" && $category != "ExifTool"){
                if (!in_array($category, $categories)) {
                    $categories[] = $category;
                }
            }
        }
        return $categories;
    }

    public function getLocation()
    {
        $meta =  self::getLatestMeta();

        if (array_key_exists('XMP:Location', $meta)) {
            return $meta['XMP:Location'];
        } elseif (array_key_exists('XMP:City', $meta)) {
            if (array_key_exists('XMP:Country', $meta)) {
                return $meta['XMP:City'] . ", " . $meta['XMP:Country'];
            }
            return $meta['XMP:City'];
        } elseif (array_key_exists('IPTC:City', $meta)) {
             return $meta['IPTC:City'];
        }
        return null;
    }

    public function hasLatestMeta()
    {
        return file_exists(ExifTools::META_PATH . $this->id . '.json.old');
    }

    public function getLatestMeta()
    {
        if ($this->latestMeta === null) {
            $this->latestMeta = ExifTools::getImgMeta($this);
        }
        return $this->latestMeta;
    }

    /**
     * Return a selection of essential Meta use in multiple place in front.
     * @return <array> containing Title, description and author
     */
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

    public function resetOriginalMeta()
    {
        ExifTools::resetOriginalMeta($this);
    }

    public function resetLastMeta()
    {
        ExifTools::resetLastMeta($this);
    }

    /**
     * Update the image metadata.
     * Specific fields need to be changed back to array,
     * then the whole set is sent to ExifTools to insert them in the image
     * and update the json files.
     *
     * @param <array> the metadata
     */
    public function updateMeta(array $meta) {
        $dataToClean = ['XMP:Subject','IPTC:Keywords'];
        foreach($dataToClean as $key) {
            if (array_key_exists($key, $meta)) {
                $arrayToClean = explode(',', $meta[$key]);
                foreach($arrayToClean as &$dataToClean) {
                    $dataToClean = trim($dataToClean);
                }
                $meta[$key] = $arrayToClean;
            }
        }
        ExifTools::setImgMeta([$meta], $this);
    }

    /**
     * Get all possible keywords from image
     * @return string
     */
    public function getTags() {
        $meta = $this->getLatestMeta();
        if (array_key_exists('XMP:Subject', $meta)) {
            return str_replace(' ', ',', implode(',', $meta['XMP:Subject']));
        } elseif (array_key_exists('IPTC:Keywords', $meta)) {
            return str_replace(' ', ',', implode($meta['IPTC:Keywords']));
        } else {
            return '';
        }
    }
}
