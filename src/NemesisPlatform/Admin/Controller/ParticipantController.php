<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 25.11.2014
 * Time: 16:17
 */

namespace NemesisPlatform\Admin\Controller;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ParticipantController
 *
 * @package NemesisPlatform\Admin\Controller
 * @Route("/participant")
 */
class ParticipantController extends Controller
{
    private static $datatablesFields = [
        'id'       => 'ud.id',
        'fio'      => 'CONCAT(CONCAT(CONCAT(CONCAT(u.lastname,\' \'),u.firstname),\' \'),u.middlename)',
        'email'    => 'u.email',
        'season'   => 'season.short_name',
        'sote'     => 'site.short_name',
        'created'  => 'ud.created',
        'status'   => 'u.status',
        'phone'    => 'p.phonenumber',
        'category' => 'category.name',
        //            'teams' => 'GROUP_CONCAT(t.name SEPARATOR \', \')'
    ];

    /**
     * @Route("/list",name="site_admin_participant_list")
     * @Method("GET")
     * @Template()
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
        $sites = [$this->get('site.provider')->getSite()];

        return ['season' => $season, 'sites' => $sites];
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/autocomplete",name="site_admin_participant_autocomplete")
     * @Method("GET")
     */
    public function autocompleteAction(Request $request)
    {
        if (!$request->query->get('term')) {
            return new JsonResponse([]);
        }

        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();
        $query   = $manager->getRepository(Participant::class)
            ->createQueryBuilder('s')
            ->select('s')
            ->leftJoin('s.user', 'u')
            ->leftJoin('s.season', 'season')
            ->leftJoin('season.site', 'site');

        $query->orWhere('u.email LIKE :term')->setParameter('term', '%'.$request->query->get('term').'%');
        $query->orWhere('u.firstname LIKE :term')->setParameter('term', '%'.$request->query->get('term').'%');
        $query->orWhere('u.lastname LIKE :term')->setParameter('term', '%'.$request->query->get('term').'%');
        $query->orWhere('u.middlename LIKE :term')->setParameter('term', '%'.$request->query->get('term').'%');

        $query->andWhere('site = :site')->setParameter('site', $this->get('site.provider')->getSite());

        return new JsonResponse(
            array_map(
                function (Participant $participant) {
                    return [
                        'label' =>
                            sprintf(
                                "[%s - %s] %s (%s)",
                                $participant->getSeason()->getSite()->getShortName(),
                                $participant->getSeason()->getShortName(),
                                $participant->getUser()->getFormattedName(),
                                $participant->getUser()->getEmail()
                            ),
                        'id'    => $participant->getId(),
                    ];
                },
                $query->getQuery()->getResult()
            )
        );
    }

    /**
     * @Route("/datable",name="site_admin_participant_datatable")
     * @Method("GET")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function datatableAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $repo = $manager->getRepository(Participant::class);
        /** @var array $fields These fields accept sorting and searching */
        $fields = self::$datatablesFields;

        $season = null;
        if ($request->get('season', null)) {
            $season = $this->getDoctrine()->getRepository(Season::class)->find($request->get('season', null));
        }

        $result = $repo->jqueryDataTableFetch(
            $request->query->all(),
            $fields,
            $this->get('site.provider')->getSite(),
            $season,
            (bool)$request->query->get('everyone', false)
        );

        /** @var Participant[] $objects */
        $objects = $result['objects'];
        $output  = $result['output'];

        foreach ($objects as $user) {
            $output['aaData'][] = $this->createDatatablesRow($user);
        }

        return new JsonResponse($output);
    }

    /**
     * @param Participant $user
     *
     * @return array
     */
    private function createDatatablesRow(Participant $user)
    {
        $row   = [];
        $row[] = $user->getId();
        $row[] = $user->getUser()->getFormattedName('%l %f %m');
        $row[] = $user->getUser()->getEmail();
        $row[] = $user->getSeason()->getShortName();
        $row[] = $user->getSeason()->getSite()->getShortName();
        $row[] = $user->getCreated()->format('Y.m.d H:i:s');
        $row[] = $user->getUser()->getStatus();
        $row[] = $user->getUser()->getPhone() ? $user->getUser()->getPhone()->getFullPhoneNumber()
            : null;
        $row[] = $user->getCategory()->getName();
        $row[] = array_map(
            function (Team $team) {
                return ['team_id' => $team->getID(), 'team_name' => $team->getName()];
            },
            $user->getTeams()->toArray()
        );

        return $row;
    }

    /**
     * @param Participant $data
     *
     * @return Response|array
     * @Route("/{data}/view",name="site_admin_participant_view")
     * @Method("GET")
     */
    public function viewAction(Participant $data)
    {
        return ['data' => $data];
    }

    /**
     * @param Participant $data
     * @Route("/{data}/remove",name="site_admin_participant_remove")
     * @Method("GET")
     *
     * @return Response
     */
    public function removeAction(Participant $data)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($data);
        $manager->flush();

        return $this->redirect($this->generateUrl('site_admin_participant_list'));
    }

    /**
     * @param Request $request
     * @Route("/create",name="site_admin_participant_create")
     * @Method({"GET","POST"})
     *
     * @return Response|array
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm('season_data_type')
            ->add('submit', 'submit', ['label' => 'Создать анкету']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            return $this->redirect($this->generateUrl('site_admin_participant_edit', ['data' => $data]));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request                                  $request
     * @param \NemesisPlatform\Game\Entity\Participant $data
     *
     * @Route("/{data}/edit",name="site_admin_participant_edit")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @return Response|array
     */
    public function editAction(Request $request, Participant $data)
    {
        $form = $this->createForm('participant', $data, ['season' => $data->getSeason()])
            ->add('submit', 'submit', ['label' => 'Обновить анкету']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Participant $data */
            $data = $form->getData();

            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('site_admin_participant_edit', ['data' => $data->getId()]));
        }

        return ['form' => $form->createView(), 'data' => $data];
    }
}
