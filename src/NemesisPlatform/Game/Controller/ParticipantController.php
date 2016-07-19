<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.06.2014
 * Time: 16:28
 */

namespace NemesisPlatform\Game\Controller;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Rule\Participant\ConfirmedPhoneRule;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\SeasonedSite;
use NemesisPlatform\Game\Repository\ParticipantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ParticipantController
 *
 * @package NemesisPlatform\Game\Controller\Site
 * @Security("has_role('ROLE_USER')")
 * @Route("/users")
 */
class ParticipantController extends Controller
{
    /**
     * @param $id
     *
     * @return Response|array
     * @Route("/{id}/view", name="site_user_view")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);

        return ['l_user' => $user];
    }


    /**
     * @param Request                             $request
     * @param \NemesisPlatform\Game\Entity\Season $season
     *
     * @return Response|array
     * @Template()
     * @Route("/account/season/{season}/register", name="site_service_update_profile")
     * @Method({"GET","POST"})
     */
    public function createAction(Request $request, Season $season)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($s_data = $user->getParticipation($season)) {
            return $this->redirect(
                $this->generateUrl('site_user_seasons_edit', ['participant' => $s_data->getId()])
            );
        }

        $form = $this
            ->createForm(
                'participant',
                new Participant(),
                ['season' => $season]
            )
            ->add('submit', 'submit', ['label' => 'Обновить']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var \NemesisPlatform\Game\Entity\Participant $participant */
            $participant = $form->getData();
            $participant->setSeason($season);
            $participant->setUser($user);

            $this->getDoctrine()->getManager()->persist($participant);

            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Успешное обновление анкеты');

            return $this->redirect($this->generateUrl('site_account_show'));
        }

        return ['season' => $season, 'form' => $form->createView()];
    }


    /**
     * @Route("/season/{season}/datatable", name="site_user_datatable")
     * @Method("GET")
     * @param Season  $season
     * @param Request $request
     *
     * @return string
     */
    public function getPublicUserListAction(Season $season, Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var ParticipantRepository $repo */
        $repo = $em->getRepository(Participant::class);
        /** @var array $fields These fields accept sorting and searching */
        $fields = [
            'id'         => 'u.id',
            'lastname'   => 'u.lastname',
            'firstname'  => 'u.firstname',
            'middlename' => 'u.middlename',
        ];

        $everyone = true;

        foreach ($season->getRules() as $rule) {
            if ($rule instanceof ConfirmedPhoneRule && $rule->isEnabled()) {
                $everyone = false;
            }
        }

        $result = $repo->jqueryDataTableFetch(
            $request->query->all(),
            $fields,
            $season->getSite(),
            $season,
            $everyone
        );
        /** @var \NemesisPlatform\Game\Entity\Participant[] $objects */
        $objects = $result['objects'];
        $output  = $result['output'];

        foreach ($objects as $user) {
            $row                = [];
            $row[]              = $user->getUser()->getID();
            $row[]              = $user->getUser()->getFormattedName('%l %f %m');
            $row[]              = $user->getCategory()->getName();
            $output['aaData'][] = $row;
        }

        return new JsonResponse($output);
    }


    /**
     * @Route("/season/{season}/list", name="site_user_list")
     * @Method("GET")
     * @Route("/list", name="site_user_list_all")*
     * @Template()
     * @param Season|null $season
     *
     * @return Response
     */
    public function listAction(Season $season = null)
    {
        /** @var SeasonedSite $site */
        $site = $this->get('site.manager')->getSite();

        if (count($site->getSeasons()) === 0) {
            throw new AccessDeniedHttpException('В данном проекте нет сезонов');
        }

        if (!$season) {
            $season = $site->getActiveSeason() ?: $site->getSeasons()->first();
        }

        return ['season' => $season];
    }

    /**
     * @param Request                             $request
     * @param \NemesisPlatform\Game\Entity\Season $season
     *
     * @return Response
     * @Route("/season/{season}/autocomplete", name="site_user_autocomplete")
     * @Method("GET")
     */
    public function autocompleteAction(Request $request, Season $season)
    {
        if (!in_array($season, $this->get('site.manager')->getSite()->getSeasons()->toArray())) {
            throw new NotFoundHttpException('Сезон не найден');
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if (!$request->query->get('term')) {
            return new JsonResponse([]);
        }

        $query = $em->getRepository(Participant::class)->createQueryBuilder('d')
            ->select('d')->join('d.user', 'u')->where('d.season = :season')->setParameter('season', $season);


        $query
            ->andWhere('u.firstname LIKE :term OR u.lastname LIKE :term OR u.middlename LIKE :term')
            ->setParameter('term', '%'.$request->query->get('term').'%')
            ->setParameter('term', '%'.$request->query->get('term').'%')
            ->setParameter('term', '%'.$request->query->get('term').'%');

        return new JsonResponse(
            array_map(
                function (Participant $user) {
                    return [
                        'label' => $user->getUser()->getFormattedName(),
                        'id'    => $user->getId(),
                    ];
                },
                $query->getQuery()->getResult()
            )
        );
    }
}
