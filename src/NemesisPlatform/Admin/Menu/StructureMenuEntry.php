<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-08-17
 * Time: 22:55
 */

namespace NemesisPlatform\Admin\Menu;

use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Symfony\Component\Routing\RouterInterface;

class StructureMenuEntry extends MenuElement
{
    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        parent::__construct();

        $this->setParent(null);
        $this->setLabel('Структура');
        $this->setIcon('gear');

        $sitesElement = new MenuElement();
        $sitesElement->setLabel('Сайты');
        $sitesElement->setLink($router->generate('site_admin_site_list'));
        $sitesElement->setTitle('Список сайтов');
        $sitesElement->setIcon('globe');

        $themesElement = new MenuElement();
        $themesElement->setLabel('Оформление');
        $themesElement->setLink($router->generate('switchable_theme_instance_list'));
        $themesElement->setTitle('Список вариантов настроек');
        $themesElement->setIcon('paint-brush');

        $usersElement = new MenuElement();
        $usersElement->setLabel('Пользователи');
        $usersElement->setLink($router->generate('site_admin_user_list'));
        $usersElement->setTitle('Список участников портала');
        $usersElement->setIcon('users');

        $blocksElement = new MenuElement();
        $blocksElement->setLabel('Блоки');
        $blocksElement->setLink($router->generate('admin_blocks_list'));
        $blocksElement->setTitle('Список блоков');
        $blocksElement->setIcon('cubes');

        $fieldsElement = new MenuElement();
        $fieldsElement->setLabel('Поля');
        $fieldsElement->setLink($router->generate('storable_forms_field_list'));
        $fieldsElement->setTitle('Список полей анкеты');
        $fieldsElement->setIcon('list-alt');

        $rulesElement = new MenuElement();
        $rulesElement->setLabel('Правила');
        $rulesElement->setLink($router->generate('admin_rule_list'));
        $rulesElement->setTitle('Список всех правил');
        $rulesElement->setIcon('exclamation-triangle');

        $certificatesElement = new MenuElement();
        $certificatesElement->setLabel('Сертификаты');
        $certificatesElement->setLink($router->generate('admin_certificates_types_list'));
        $certificatesElement->setTitle('Управление типами сертификатов');
        $certificatesElement->setIcon('certificate');

        $this->setChildren(
            [
                $sitesElement,
                $themesElement,
                $usersElement,
                $blocksElement,
                $fieldsElement,
                $rulesElement,
                $certificatesElement,
            ]
        );
    }
}
