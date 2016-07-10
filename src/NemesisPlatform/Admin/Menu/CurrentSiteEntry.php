<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-08-17
 * Time: 22:55
 */

namespace NemesisPlatform\Admin\Menu;

use NemesisPlatform\Components\MultiSite\Service\SiteManagerService;
use NemesisPlatform\Core\CMS\Entity\MenuElement;
use NemesisPlatform\Game\Service\FallbackSite;
use Symfony\Component\Routing\RouterInterface;

class CurrentSiteEntry extends MenuElement
{
    /**
     * @param SiteManagerService $siteManager
     * @param RouterInterface    $router
     */
    public function __construct(SiteManagerService $siteManager, RouterInterface $router)
    {
        parent::__construct();

        $site = $siteManager->getSite();

        if (!$site || $site instanceof FallbackSite) {
            $this->setDisabled(true);

            return;
        }

        $this->setParent(null);
        $this->setLabel($site->getShortName());
        $this->setIcon('flag');

        $settingsChild = new MenuElement();
        $settingsChild->setLabel('Настройки');
        $settingsChild->setLink($router->generate('site_admin_site_show', ['site' => (string)$site]));
        $settingsChild->setTitle('Настройки сайта');
        $settingsChild->setIcon('gear');

        $pagesChild = new MenuElement();
        $pagesChild->setLabel('Страницы');
        $pagesChild->setLink($router->generate('site_admin_page_list'));
        $pagesChild->setTitle('Страницы');
        $pagesChild->setIcon('file-text-o');

        $menuChild = new MenuElement();
        $menuChild->setLabel('Меню');
        $menuChild->setLink($router->generate('site_admin_menu_list'));
        $menuChild->setTitle('Меню');
        $menuChild->setIcon('navicon');

        $newsChild = new MenuElement();
        $newsChild->setLabel('Новости');
        $newsChild->setLink($router->generate('site_admin_news_list'));
        $newsChild->setTitle('Новости');
        $newsChild->setIcon('newspaper-o');

        $categoriesElement = new MenuElement();
        $categoriesElement->setLabel('Категории');
        $categoriesElement->setLink($router->generate('site_admin_usercategory_list'));
        $categoriesElement->setTitle('Список категорий пользователей');
        $categoriesElement->setIcon('cubes');

        $teamsChild = new MenuElement();
        $teamsChild->setLabel('Команды');
        $teamsChild->setLink($router->generate('site_admin_team_list'));
        $teamsChild->setTitle('Список команд');
        $teamsChild->setIcon('users');

        $usersChild = new MenuElement();
        $usersChild->setLabel('Участники');
        $usersChild->setLink($router->generate('site_admin_participant_list'));
        $usersChild->setTitle('Участники сезонов');
        $usersChild->setIcon('user');

        $survey = new MenuElement();
        $survey->setTitle('Опросы');
        $survey->setLabel('Опросы');
        $survey->setIcon('check-square-o');
        $survey->setLink($router->generate('admin_survey_list'));

        $this->setChildren(
            [
                $settingsChild,
                $pagesChild,
                $newsChild,
                $menuChild,
                $categoriesElement,
                $teamsChild,
                $usersChild,
                $survey,
            ]
        );
    }
}
