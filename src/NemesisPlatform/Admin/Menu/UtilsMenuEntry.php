<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.10.2014
 * Time: 16:24
 */

namespace NemesisPlatform\Admin\Menu;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Routing\RouterInterface;

class UtilsMenuEntry extends MenuElement
{
    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        parent::__construct();

        $this->setParent(null);
        $this->setLabel('Утилиты');
        $this->setIcon('gear');

        $tagListElement = new MenuElement();
        $tagListElement->setLabel('Управление тегами');
        $tagListElement->setLink($router->generate('site_admin_utils_tag_list'));
        $tagListElement->setTitle('Просмотр и редактирование тегов');
        $tagListElement->setIcon('tags');

        $exportElement = new MenuElement();
        $exportElement->setLabel('Экспорт данных');
        $exportElement->setLink($router->generate('exporters_list'));
        $exportElement->setTitle('Список доступных экспортеров');
        $exportElement->setIcon('upload');

        $importElement = new MenuElement();
        $importElement->setLabel('Импорт данных');
        $importElement->setLink($router->generate('importers_list'));
        $importElement->setTitle('Список доступных импортеров');
        $importElement->setIcon('download');

        $generatorsElement = new MenuElement();
        $generatorsElement->setLabel('Генерация данных');
        $generatorsElement->setLink($router->generate('admin_generators_list'));
        $generatorsElement->setTitle('Список доступных генераторв');
        $generatorsElement->setIcon('flask');

        $this->setChildren([$tagListElement, $exportElement, $importElement, $generatorsElement]);
    }
}
