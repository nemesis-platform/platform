<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 07.07.2015
 * Time: 14:40
 */

namespace NemesisPlatform\Core\CMS\Entity\Block;

use NemesisPlatform\Admin\Form\Type\Block\CustomContentBlockType;

class CustomContentBlock extends AbstractBlock
{
    /** @var  string */
    private $content;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param array $options
     *
     * @return string
     */
    public function getTemplate(array $options = [])
    {
        return 'NemesisCmsBundle:Block:custom_content.html.twig';
    }

    /**
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'block_custom_content';
    }

    public function getOptions()
    {
        return ['content' => $this->content];
    }

    public function getFormType()
    {
        return new CustomContentBlockType(get_class($this));
    }
}
