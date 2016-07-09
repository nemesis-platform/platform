<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.05.2015
 * Time: 12:24
 */

namespace NemesisPlatform\Game\Entity\Certificate;

class AbstractCertificate
{
    /** @var  int|null */
    private $id;
    /** @var  string */
    private $link;
    /** @var  CertificateType */
    private $type;

    /**
     * AbstractCertificate constructor.
     *
     * @param string          $link
     * @param CertificateType $type
     */
    public function __construct($link, CertificateType $type)
    {
        $this->link = $link;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }


    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return CertificateType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param CertificateType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
