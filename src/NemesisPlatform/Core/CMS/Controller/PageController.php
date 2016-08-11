<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 17.12.2014
 * Time: 16:21
 */

namespace NemesisPlatform\Core\CMS\Controller;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Core\CMS\Entity\Page;
use NemesisPlatform\Core\CMS\Entity\ProxyPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{

    /**
     * @Route("/", name="display_front_page")
     * @Method("GET")
     * @param $alias string
     * @Template()
     *
     * @return Response
     */
    public function showAction($alias = "_front")
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->getDoctrine()->getManager();

        /** @var ProxyPage $page */
        $page = $manager->getRepository(ProxyPage::class)->findOneBy(
            ['alias' => $alias, 'site' => $this->get('site.provider')->getSite()]
        );


        if ($page) {
            return $this->forward($this->convertRouteToController($page->getRoute()), $page->getData());
        }

        /** @var Page $page */
        $page = $manager->getRepository(Page::class)->findOneBy(
            ['alias' => $alias, 'site' => $this->get('site.provider')->getSite()]
        );

        if (!$page) {
            throw new NotFoundHttpException('Page not found');
        }

        return ['page' => $page];
    }

    private function convertRouteToController($routeName)
    {
        $routes = $this->get('router')->getRouteCollection();

        return $routes->get($routeName)->getDefaults()['_controller'];
    }

    /**
     * @Route("/register/activated", name="site_service_login_activated")
     * @Method("GET")
     * @return Response
     * @Template("NemesisCmsBundle:Page:activation_succeeded.html.twig")
     */
    public function activationSucceededPageAction()
    {
        return [];
    }

    /**
     * @Route("/register/success", name="site_service_register_success")
     * @Method("GET")
     * @Template("NemesisCmsBundle:Page:activation_needed.html.twig")
     * @return Response
     */
    public function activationNeededPageAction()
    {
        return [];
    }
}
