<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-26
 * Time: 23:46
 */

namespace NemesisPlatform\Core\CMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class MenuElement
{
    const TYPE_LINK      = 0;
    const TYPE_DROPDOWN  = 1;
    const TYPE_DELIMITER = 2;
    /** @var null|int */
    private $id = null;
    /** @var MenuElement|null */
    private $parent = null;
    /** @var string */
    private $label;
    /** @var null|string */
    private $link = null;
    /** @var null|string */
    private $title = null;
    /** @var string Additional CSS classes */
    private $classes = '';
    /** @var int */
    private $type = self::TYPE_LINK;
    /** @var int */
    private $weight = 0;
    /** @var  MenuElement[]|ArrayCollection */
    private $children;

    private $icon = null;

    private $badge = null;

    private $disabled = false;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function addChild(MenuElement $element)
    {
        if (!$this->children->contains($element)) {
            $this->children->add($element);
        }
    }

    public function removeChild(MenuElement $element)
    {
        if ($this->children->contains($element)) {
            $this->children->removeElement($element);
            $element->setParent(null);
        }
    }

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param boolean $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    public function getBadge()
    {
        return $this->badge;
    }

    public function setBadge($type, $is_number = false, $value = null)
    {
        $this->badge              = [];
        $this->badge['type']      = $type;
        $this->badge['is_number'] = $is_number;
        $this->badge['value']     = $value;
    }

    /**
     * @return null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param null $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return MenuElement[]|ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param MenuElement[] $children
     */
    public function setChildren($children)
    {
        $this->children = new ArrayCollection($children);
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


    /**
     * @return string
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param string $classes
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return boolean
     */
    public function isDelimiter()
    {
        return $this->type === self::TYPE_DELIMITER;
    }

    /**
     * @return boolean
     */
    public function isDropdown()
    {
        return count($this->children) > 0 || $this->type === self::TYPE_DROPDOWN;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return null|string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param null|string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return MenuElement|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param MenuElement|null $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return null|string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
}
