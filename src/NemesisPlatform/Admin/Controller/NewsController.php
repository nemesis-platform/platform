<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.06.2014
 * Time: 11:11
 */

namespace NemesisPlatform\Admin\Controller;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Core\CMS\Entity\News;
use NemesisPlatform\Game\Entity\SeasonedSite;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NewsController
 *
 * @package NemesisPlatform\Admin\Controller\Site
 * @Route("/news")
 */
class NewsController extends Controller
{
    /**
     * @return Response
     * @Template()
     * @Route("/list", name="site_admin_news_list")
     * @Method("GET")
     */
    public function listAction()
    {
        /** @var SeasonedSite $site */
        $site = $this->get('site.provider')->getSite();
        /** @var \NemesisPlatform\Core\CMS\Entity\News[] $news */
        $news = $this->getDoctrine()->getManager()->getRepository(News::class)->findBy(
            ['season' => $site->getSeasons()->toArray()],
            ['date' => 'DESC']
        );

        return ['news' => $news];
    }

    /**
     * @param         $news
     *
     * @return Response
     * @Route("/{news}/delete", name="site_admin_news_delete")
     * @Method("GET")
     */
    public function deleteAction(News $news)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($news);
        $manager->flush();

        return $this->redirect($this->generateUrl('site_admin_news_list'));
    }

    /**
     * @param Request                                    $request
     * @param \NemesisPlatform\Core\CMS\Entity\News $news
     *
     * @return Response
     * @Route("/{news}/edit", name="site_admin_news_edit")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function editAction(Request $request, News $news)
    {
        $form = $this->createForm('news_entry_type', $news)
                     ->add('submit', 'submit', ['label' => 'Сохранить']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect(
                $this->generateUrl('site_admin_news_edit', ['news' => $news->getID()])
            );
        }


        return ['form' => $form->createView(), 'news' => $news];
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/create", name="site_admin_news_create")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function createAction(Request $request)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm('news_entry_type')
                     ->add('submit', 'submit', ['label' => 'Создать новость']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $news = $form->getData();

            $manager->persist($news);
            $manager->flush();

            return $this->redirect(
                $this->generateUrl('site_admin_news_edit', ['news' => $news->getID()])
            );
        }

        return ['form' => $form->createView()];
    }
}
