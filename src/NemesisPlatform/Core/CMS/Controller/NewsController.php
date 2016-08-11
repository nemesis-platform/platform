<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 17.12.2014
 * Time: 16:07
 */

namespace NemesisPlatform\Core\CMS\Controller;

use NemesisPlatform\Core\CMS\Entity\News;
use NemesisPlatform\Game\Entity\SeasonedSite;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsController extends Controller
{

    /**
     * @Route("/news", name="site_news_list")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        /** @var SeasonedSite $site */
        $site = $this->get('site.provider')->getSite();

        $query = $this->getDoctrine()->getRepository(News::class)
                      ->createQueryBuilder('news')
                      ->where('news.season IN (:seasons)')
                      ->setParameter('seasons', $site->getSeasons()->toArray())
                      ->orderBy('news.date', 'DESC')
                      ->getQuery();

        /** @var SlidingPagination|\NemesisPlatform\Core\CMS\Entity\News[] $news */
        $news = $this->get('knp_paginator')->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 12)
        );

        return ['news' => $news];
    }

    /**
     * @Route("/news/{id}", name="site_news_show")
     * @Method("GET")
     * @Template()
     * @param $newsEntry
     *
     * @return Response
     */
    public function showAction(News $newsEntry)
    {
        if (!$newsEntry->isVisible()) {
            throw new NotFoundHttpException('Новость не найдена');
        }

        return ['newsEntry' => $newsEntry];
    }
}
