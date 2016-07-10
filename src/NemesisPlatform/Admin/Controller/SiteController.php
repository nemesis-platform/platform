<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.08.2014
 * Time: 9:49
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\CMS\Entity\ProxyPage;
use NemesisPlatform\Game\Entity\SeasonedSite;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SiteController
 *
 * @package NemesisPlatform\Admin\Controller
 * @Route("/sites")
 */
class SiteController extends Controller
{
    /**
     * @Route("/create", name="site_admin_site_create")
     * @Template()
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $site = new SeasonedSite($request->getHost(), 'Текущий сайт');

        $form = $this->createForm('site', $site);
        $form->add('submit', 'submit', ['label' => 'Создать сайт']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($site);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Сайт успешно создан');

            return $this->redirect(
                $this->generateUrl('site_admin_site_edit', ['site' => $site->getId()])
            );
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param $site
     *
     * @return array
     * @Route("/{site}/show", name="site_admin_site_show")
     * @Template()
     */
    public function showAction($site)
    {
        $site = $this->getDoctrine()->getManager()->find(SiteInterface::class, $site);
        if (null === $site) {
            throw $this->createNotFoundException();
        }

        $pages = $this->getDoctrine()->getManager()->getRepository(ProxyPage::class)->findBy(
            ['site' => $site]
        );

        return ['site' => $site, 'proxy_pages' => $pages];
    }

    /**
     * @Route("/list", name="site_admin_site_list")
     * @Template()
     */
    public function listAction()
    {
        $sites = $this->getDoctrine()->getRepository(SeasonedSite::class)->findAll();

        return ['sites' => $sites];
    }

    /**
     * @param $site
     *
     * @return array
     * @Route("/{site}/delete", name="site_admin_site_delete")
     */
    public function deleteAction($site)
    {
        $site = $this->getDoctrine()->getManager()->find(SiteInterface::class, $site);

        $this->getDoctrine()->getManager()->remove($site);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('site_admin_site_list'));
    }

    /**
     * @param Request       $request
     * @param SiteInterface $site
     *
     * @return array
     * @Route("/{site}/edit", name="site_admin_site_edit")
     * @Template()
     */
    public function editAction(Request $request, $site)
    {
        $site = $this->getDoctrine()->getManager()->find(SiteInterface::class, $site);

        $form = $this->createForm('site', $site);
        $form->add('submit', 'submit', ['label' => 'Обновить сайт']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Сайт успешно обновлен');

            return $this->redirect(
                $this->generateUrl('site_admin_site_edit', ['site' => $site])
            );
        }

        return ['site' => $site, 'form' => $form->createView()];
    }
}
