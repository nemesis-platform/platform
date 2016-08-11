<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.06.2014
 * Time: 11:10
 */

namespace NemesisPlatform\Admin\Controller;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Core\CMS\Entity\Menu;
use NemesisPlatform\Core\CMS\Entity\MenuElement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MenuController
 *
 * @package NemesisPlatform\Admin\Controller\Site
 * @Route("/site/menu")
 */
class MenuController extends Controller
{
    /**
     * @return Response
     * @Route("/list", name="site_admin_menu_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        $menus = $this->getDoctrine()->getManager()->getRepository(Menu::class)->findBy(
            ['site' => $this->get('site.provider')->getSite()]
        );

        return ['menus' => $menus];
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/add", name="site_admin_menu_create")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function createAction(Request $request)
    {
        /** @var EntityManager $em */
        $em   = $this->getDoctrine()->getManager();
        $form = $this
            ->createForm('menu_type')
            ->add('submit', 'submit', ['label' => 'Создать меню']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var Menu $menu */
            $menu = $form->getData();
            $menu->sanitize();

            $em->persist($menu);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Меню успешно создано');

            return $this->redirect(
                $this->generateUrl('site_admin_menu_edit', ['menu' => $menu->getId()])
            );
        }

        return ['form' => $form->createView()];
    }

    /**
     *
     * @param \NemesisPlatform\Core\CMS\Entity\Menu $menu
     *
     * @return RedirectResponse
     * @Route("/{menu}/delete", name="site_admin_menu_delete")
     * @Method("GET")
     */
    public function deleteAction(Menu $menu)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $rr = function (MenuElement $element) use (&$rr, $manager) {
            $element->setParent(null);
            foreach ($element->getChildren() as $child) {
                $element->getChildren()->removeElement($child);
                $child->setParent(null);
                if ($rr($child)) {
                    $manager->remove($child);
                }
            }

            return true;
        };

        foreach ($menu->getElements() as $el) {
            if ($rr($el)) {
                $manager->remove($el);
            }
        }

        $manager->remove($menu);
        $manager->flush();

        $this->get('session')->getFlashBag()->add('warning', 'Меню успешно удалено');

        return $this->redirect($this->generateUrl('site_admin_menu_list'));
    }

    /**
     * @param Request $request
     * @param Menu    $menu
     *
     * @return Response
     * @Route("/{menu}/edit", name="site_admin_menu_edit")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function editAction(Request $request, Menu $menu)
    {
        $form = $this->createForm('menu_type', $menu)
            ->add('submit', 'submit', ['label' => 'Обновить меню']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $menu->sanitize();
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Меню успешно обновлено');

            return $this->redirect(
                $this->generateUrl('site_admin_menu_edit', ['menu' => $menu->getId()])
            );
        }


        return ['form' => $form->createView(), 'menu' => $menu];
    }
}
