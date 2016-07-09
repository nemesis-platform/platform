<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.06.2014
 * Time: 14:29
 */

namespace NemesisPlatform\Core\CMS\Entity;

class File
{
    /**
     * @var null|int
     */
    private $id = null;
    /**
     * @var string
     */
    private $filename = '';
    /**
     * @var string
     */
    private $mime = '';
    /**
     * @var string
     */
    private $storage_id = '';

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @param string $mime
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
    }

    /**
     * @return string
     */
    public function getStorageId()
    {
        return $this->storage_id;
    }

    /**
     * @param string $storage_id
     */
    public function setStorageId($storage_id)
    {
        $this->storage_id = $storage_id;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function remove()
    {
        unlink($this->getInternalPath());
    }

    public function getInternalPath()
    {
        return $this->storage_id;
//        return REPORTS_STORAGE . '/' . $this->storage_id;
    }
}
