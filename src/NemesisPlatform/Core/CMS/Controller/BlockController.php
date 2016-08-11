<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.07.2015
 * Time: 15:02
 */

namespace NemesisPlatform\Core\CMS\Controller;

use NemesisPlatform\Core\CMS\Entity\News;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends Controller
{
    /**
     * @Template("NemesisCmsBundle:Block:side_news_controller.html.twig")
     *
     * @param int $count
     *
     * @return Response
     */
    public function renderSideNewsAction($count = 3)
    {
        $news = $this->getDoctrine()->getManager()->getRepository(News::class)->getLastNews(
            $this->get('site.provider')->getSite()->getSeasons()->toArray(),
            $count
        );

        return ['news' => $news];
    }
}
