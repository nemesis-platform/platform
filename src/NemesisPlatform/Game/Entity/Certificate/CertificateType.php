<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.05.2015
 * Time: 12:23
 */

namespace NemesisPlatform\Game\Entity\Certificate;

class CertificateType
{
    private $id;
    private $name;
    private $certificates;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCertificates()
    {
        return $this->certificates;
    }

    /**
     * @param mixed $certificates
     */
    public function setCertificates($certificates)
    {
        $this->certificates = $certificates;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
