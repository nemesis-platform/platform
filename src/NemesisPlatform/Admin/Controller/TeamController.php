<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.06.2014
 * Time: 11:18
 */

namespace NemesisPlatform\Admin\Controller;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Game\Repository\TeamRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class TeamController
 *
 * @package NemesisPlatform\Admin\Controller\Users
 * @Route("/teams")
 */
class TeamController extends Controller
{
    private static $datatablesFields = [
        'id'      => 't.id',
        'name'    => 't.name',
        'season'  => 'season.name',
        'league'  => 'l.name',
        'captain' => 'cu.lastname',
        'email'   => 'cu.email',
    ];

    /**
     * @Route("/list", name="site_admin_team_list")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     *
     * @return Response|array
     */
    public function listAction(Request $request)
    {
        $season = null;
        if ($request->get('season', null)) {
            $season = $this->getDoctrine()->getRepository(Season::class)->find($request->get('season', null));
        }

        /** @var SiteInterface[] $sites */
        $sites = [$this->get('site.manager')->getSite()];

        return ['season' => $season, 'sites' => $sites];
    }

    /**
     * @param Request                           $request
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return Response|array
     * @Route("/{id}/edit", name="site_admin_team_edit")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function editAction(Request $request, Team $team)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('team_type', $team)
            ->add('submit', 'submit', ['label' => 'Обновить команду']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            return $this->redirect(
                $this->generateUrl('site_admin_team_edit', ['id' => $team->getID()])
            );
        }

        return ['team' => $team, 'form' => $form->createView()];
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/autocomplete", name="site_admin_team_datatable")
     * @Method("GET")
     */
    public function datatableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var TeamRepository $repo */
        $repo = $em->getRepository(Team::class);
        /** @var array $fields These fields accept sorting and searching */
        $fields = self::$datatablesFields;

        $site = $this->get('site.manager')->getSite();

        $season = null;
        if ($request->get('season', null)) {
            $season = $this->getDoctrine()->getRepository(Season::class)->find($request->get('season', null));
            if ($season->getSite() !== $site) {
                throw new AccessDeniedHttpException('No access to this season from this site');
            }
        }

        $result = $repo->jqueryDataTableFetch(
            $request->query->all(),
            $fields,
            $site,
            $season
        );
        /** @var \NemesisPlatform\Game\Entity\Team[] $objects */
        $objects = $result['objects'];
        $output  = $result['output'];

        foreach ($objects as $team) {
            $output['aaData'][] = $this->createDatatablesRow($team);
        }

        return new JsonResponse($output);
    }

    /**
     * @param Team $team
     *
     * @return array
     */
    private function createDatatablesRow(Team $team)
    {
        $row   = [];
        $row[] = $team->getID();
        $row[] = $team->getName();
        $row[] = $team->getSeason()->getShortName();
        $row[] = $team->getSeason()->getSite()->getShortName();
        $row[] = $team->getLeague() ? $team->getLeague()->getName() : '';
        $row[] = $team->getCaptain() ? $team->getCaptain()->getId() : null;
        $row[] = $team->getCaptain() ? $team->getCaptain()->getUser()->getFormattedName('%l') : null;
        $row[] = $team->getCaptain() ? $team->getCaptain()->getUser()->getEmail() : null;
        $row[] = $team->getDate()->format('Y.m.d H:i:s');
        $row[] = $team->getFormDate() ? $team->getFormDate()->format('Y.m.d H:i:s') : null;

        return $row;
    }
}
