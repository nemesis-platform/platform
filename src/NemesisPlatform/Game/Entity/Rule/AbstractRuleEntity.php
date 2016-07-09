<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.02.2015
 * Time: 12:43
 */

namespace NemesisPlatform\Game\Entity\Rule;

use InvalidArgumentException;
use NemesisPlatform\Admin\Form\Type\Rule\AbstractRuleFormType;
use Symfony\Component\Form\FormTypeInterface;

abstract class AbstractRuleEntity implements RuleInterface
{
    /** @var bool True if rule is enabled */
    protected $enabled = true;
    /** @var bool True if rule passing is essential */
    protected $strict = false;
    /** @var string */
    protected $description = '';
    /** @var  int|null */
    private $id;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


    /**
     * @param      $subject
     * @param null $context Checking context
     *
     * @return bool True if isValid is successful, false otherwise
     */
    public function isValid($subject, $context = null)
    {
        if (!$this->isApplicable($subject)) {
            throw new InvalidArgumentException("Unsupported object for checking");
        }

        return $this->check($subject, $context);
    }

    /**
     * @param $subject
     * @param $context
     *
     * @return bool
     */
    abstract protected function check($subject, $context);

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /** @return FormTypeInterface */
    public function getFormType()
    {
        return new AbstractRuleFormType();
    }

    public function __toString()
    {
        return sprintf(
            '[%s][%s][%s] %s',
            $this->id,
            $this->isStrict() ? 'S' : 'T',
            $this->isEnabled() ? 'E' : 'D',
            $this->description
        );
    }

    /**
     * @return boolean
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * @param boolean $strict
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
}
