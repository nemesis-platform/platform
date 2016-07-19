<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.12.2014
 * Time: 13:34
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Admin\Form\Type\ProxyPageType;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\CMS\Entity\ProxyPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProxyPageController
 *
 * @package NemesisPlatform\Admin\Controller
 * @Route("/proxy_pages")
 */
class ProxyPageController extends Controller
{
    /**
     * @Route("/site/{site}/list", name="admin_proxy_pages_list")
     * @Method("GET")
     * @Template()
     * @param SiteInterface $site
     *
     * @return Response
     */
    public function listAction(SiteInterface $site)
    {
        $pages = $this->getDoctrine()->getManager()->getRepository(ProxyPage::class)->findBy(
            ['site' => $site]
        );

        return ['pages' => $pages, 'site' => $site];
    }

    /**
     * @Route("/site/{site}/create", name="admin_proxy_pages_create")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param SiteInterface    $site
     *
     * @return Response
     */
    public function createAction(Request $request, SiteInterface $site)
    {
        $form = $this->createForm(new ProxyPageType($this->get('router')))
                     ->add('submit', 'submit', ['label' => 'Создать прокси-страницу']);
        $form->get('site')->setData($site);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $page = $form->getData();
            $this->getDoctrine()->getManager()->persist($page);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Прокси-страница создана');

            return $this->redirectToRoute('admin_proxy_pages_edit', ['page' => $page->getId()]);
        }

        return ['form' => $form->createView(), 'site' => $site];
    }

    /**
     * @Route("/{page}/delete", name="admin_proxy_pages_delete")
     * @Method("GET")
     * @param ProxyPage $page
     *
     * @return Response
     */
    public function deleteAction(ProxyPage $page)
    {
        $this->getDoctrine()->getManager()->remove($page);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Прокси-страница успешно удалена');

        return $this->redirectToRoute('admin_proxy_pages_list', ['site' => (string)$page->getSite()]);
    }

    /**
     * @Route("/{page}/edit", name="admin_proxy_pages_edit")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request   $request
     * @param ProxyPage $page
     *
     * @return Response
     */
    public function editAction(Request $request, ProxyPage $page)
    {
        $form = $this->createForm(new ProxyPageType($this->get('router')), $page)
                     ->add('submit', 'submit', ['label' => 'Обновить прокси-страницу']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Прокси-страница обновлена');

            return $this->redirectToRoute('admin_proxy_pages_edit', ['page' => $page->getId()]);
        }

        return ['form' => $form->createView(), 'site' => $page->getSite(), 'page' => $page];
    }
}
