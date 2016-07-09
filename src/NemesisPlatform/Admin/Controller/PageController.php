<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.06.2014
 * Time: 11:10
 */

namespace NemesisPlatform\Admin\Controller;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Core\CMS\Entity\Page;
use NemesisPlatform\Core\CMS\Entity\PageRevision;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PageController
 *
 * @package NemesisPlatform\Admin\Controller\Site
 * @Route("/site/pages")
 */
class PageController extends Controller
{
    /**
     * @return Response
     * @Template()
     * @Route("/list", name="site_admin_page_list")
     */
    public function listAction()
    {
        $pages = $this->getDoctrine()->getManager()->getRepository(Page::class)->findBy(
            ['site' => $this->get('site.manager')->getSite()]
        );

        return ['pages' => $pages];
    }

    /**
     * @param $page
     *
     * @return RedirectResponse
     * @Route("/{page}/delete", name="site_admin_page_delete")
     */
    public function deleteAction(Page $page)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        foreach ($page->getRevisions() as $rev) {
            $rev->setPage(null);
            $manager->remove($rev);
        }

        $page->setLastRevision(null);
        $manager->flush();
        $manager->remove($page);
        $manager->flush();


        return $this->redirect($this->generateUrl('site_admin_page_list'));
    }

    /**
     * @Route("/{page}/revisions", name="site_admin_revisions_list")
     * @Template()
     * @param \NemesisPlatform\Core\CMS\Entity\Page $page
     *
     * @return Response
     */
    public function listRevisionsAction(Page $page)
    {
        return ['page' => $page];
    }

    /**
     * @Route("/{page}/revisions/{revision}", name="site_admin_revisions_view")
     * @Template()
     * @param Page         $page
     * @param PageRevision $revision
     *
     * @return Response
     */
    public function showRevisionAction(Page $page, PageRevision $revision)
    {
        if (!$page->getRevisions()->contains($revision)) {
            throw new LogicException('У страницы нет такой ревизии');
        }

        return ['page' => $page, 'revision' => $revision];
    }

    /**
     * @Route("/{page}/revisions/{revision}/switch", name="site_admin_revision_switch")
     * @param \NemesisPlatform\Core\CMS\Entity\Page $page
     * @param PageRevision                               $revision
     *
     * @return Response
     */
    public function switchRevisionAction(Page $page, PageRevision $revision)
    {
        if ($page->getRevisions()->contains($revision)) {
            $page->setLastRevision($revision);
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Ревизия изменена');

            return $this->redirect(
                $this->generateUrl('site_admin_page_edit', ['page' => $page->getId()])
            );
        }

        $this->get('session')->getFlashBag()->add('success', 'Ревизия не найдена');

        return $this->redirect($this->generateUrl('site_admin_revisions_list'));
    }


    /**
     * @param Request $request
     * @param         $page
     *
     * @return Response
     * @Template()
     * @Route("/{page}/edit", name="site_admin_page_edit")
     */
    public function editAction(Request $request, Page $page)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $form = $this
            ->createForm('page_type', $page)
            ->add('submit', 'submit', ['label' => 'Сохранить']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->persist($page->getLastRevision());
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Успешное сохранение страницы');

            return $this->redirect($this->generateUrl('site_admin_page_edit', ['page' => $page->getId()]));
        }

        return ['page' => $page, 'form' => $form->createView()];
    }

    /**
     * @Route("/create", name="site_admin_page_create")
     * @Template()
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $form    = $this
            ->createForm('page_type')
            ->add('submit', 'submit', ['label' => 'Сохранить']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var \NemesisPlatform\Core\CMS\Entity\Page $page */
            $page = $form->getData();
            $page->setAuthor($this->getUser());
            $manager->persist($page);
            $manager->persist($page->getLastRevision());
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Успешное создание страницы');

            return $this->redirect(
                $this->generateUrl('site_admin_page_edit', ['page' => $page->getId()])
            );
        }

        return ['form' => $form->createView()];
    }
}
