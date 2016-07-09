<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.08.2014
 * Time: 18:18
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Game\Entity\Season;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SeasonController
 * @Route("/seasons")
 */
class SeasonController extends Controller
{

    /**
     * @return array
     * @Route("/list", name="site_admin_season_list")
     */
    public function listAction()
    {
        return [];
    }

    /**
     * @param Request $request
     * @param         $season
     *
     * @return array
     * @Route("/{season}/edit", name="site_admin_season_edit")
     * @Template()
     */
    public function editAction(Request $request, Season $season)
    {
        $form = $this->createForm('season', $season);

        $form->add('submit', 'submit', ['label' => 'Обновить сезон']);
        $form->handleRequest($request);

        if ($request->getMethod() === 'POST') {
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->persist($season);
                $this->getDoctrine()->getManager()->flush();

                $this->get('session')->getFlashBag()->add('success', 'Сезон успешно обновлен');

                return $this->redirect(
                    $this->generateUrl('site_admin_season_edit', ['season' => $season->getId()])
                );
            } else {
                $this->get('session')->getFlashBag()->add('danger', 'Ошибка в данных. Изменения не сохранены.');
            }
        }

        return ['season' => $season, 'form' => $form->createView()];
    }

    /**
     * @param Season $season
     *
     * @return array
     * @Route("/{season}/delete", name="site_admin_season_delete")
     */
    public function deleteAction(Season $season)
    {
        $this->getDoctrine()->getManager()->remove($season);
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('success', 'Успешное удаление сезона');

        return $this->redirect($this->generateUrl('site_admin_season_list'));
    }

    /**
     * @param Request       $request
     * @param SiteInterface $site
     *
     * @return array
     * @Route("/site/{site}/create", name="site_admin_season_create")
     * @Template()
     */
    public function createAction(Request $request, $site)
    {
        $site = $this->getDoctrine()->getManager()->find(SiteInterface::class, $site);

        $form = $this->createForm('season', null, ['site' => $site]);

        $form->add('submit', 'submit', ['label' => 'Создать сезон']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Season $season */
            $season = $form->getData();
            $this->getDoctrine()->getManager()->persist($season);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Успешное создание сезона');

            return $this->redirect(
                $this->generateUrl('site_admin_season_edit', ['season' => $season->getId()])
            );
        }

        return ['form' => $form->createView()];
    }
}
