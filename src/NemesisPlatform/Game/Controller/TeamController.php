<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.06.2014
 * Time: 11:50
 */

namespace NemesisPlatform\Game\Controller;

use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Rule\Participant\SingleTeamRule;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\SeasonedSite;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Game\Repository\TeamRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TeamController
 *
 * @package NemesisPlatform\Game\Controller\Account
 * @Route("/game/teams")
 * @Security("has_role('ROLE_USER')")
 */
class TeamController extends Controller
{
    /**
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return Response
     * @Route("/{team}/view", name="team_view")
     * @Template()
     */
    public function showAction(Team $team)
    {
        return ['team' => $team];
    }

    /**
     * @Route("/season/{season}/list", name="team_list")
     * @Route("/list", name="team_list_all")
     * @Template()
     * @param \NemesisPlatform\Game\Entity\Season $season
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
     * @param Request                                $request
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return Response
     * @Route("/{team}/edit", name="team_edit")
     * @Template()
     * @Security("is_granted('manage',team)")
     */
    public function editAction(Request $request, Team $team)
    {
        $form = $this->createForm('team_type', $team, ['season' => $team->getSeason()])
                     ->add('submit', 'submit', ['label' => 'Сохранить']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Команда отредактирована');

            return $this->redirectToRoute('site_account_show');
        }

        return ['form' => $form->createView(), 'team' => $team];
    }

    /**
     * @param \NemesisPlatform\Game\Entity\Season $season
     * @param Request                                  $request
     *
     * @throws NotFoundHttpException
     * @return Response
     * @Route("/season/{season}/create", name="team_create")
     * @Security("is_granted('create_team',season)")
     * @Template()
     */
    public function createAction(Season $season, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user        = $this->getUser();
        $participant = $user->getParticipation($season);

        if (count($participant->getTeams()) > 0) {
            foreach ($season->getRules() as $rule) {
                if ($rule instanceof SingleTeamRule && $rule->isEnabled()) {
                    throw new AccessDeniedHttpException(
                        'Вы уже состоите в команде. Покиньте или расформируйте команду, чтобы создать новую'
                    );
                }
            }
        }

        $form = $this->createForm('team_type', null, ['season' => $season])
                     ->add('submit', 'submit', ['label' => 'Создать']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $team = $form->getData();

            $team->setMembers([$participant]);
            $team->setSeason($season);
            $team->setCaptain($participant);

            $manager->persist($team);
            $manager->flush();

            $this->get('security.token_storage')->getToken()->setAuthenticated(false);
            $this->addFlash('success', 'Успешное создание команды');

            return $this->redirectToRoute('site_account_show');
        }

        return ['form' => $form->createView()];
    }


    /**
     * @Route("/{team}/accept/{data}", name="team_request_accept")
     * @param \NemesisPlatform\Game\Entity\Team        $team
     * @param \NemesisPlatform\Game\Entity\Participant $data
     *
     * @return RedirectResponse
     * @Security("is_granted('accept_request',team)")
     */
    public function acceptUserAction(Team $team, Participant $data)
    {
        if ($team->getSeason() !== $data->getSeason()) {
            throw new InvalidArgumentException('Несоответствие сезона команды и участника');
        }

        if (!$team->getRequests()->contains($data)) {
            throw new InvalidArgumentException('Несуществующая заявка');
        }

        $single_team = false;
        $season      = $team->getSeason();
        foreach ($season->getRules() as $rule) {
            if ($rule instanceof SingleTeamRule && $rule->isEnabled()) {
                $single_team = true;
            }
        }

        if ($single_team) {
            foreach ($data->getTeams() as $dataTeam) {
                $dataTeam->getMembers()->removeElement($data);
                $data->getTeams()->removeElement($dataTeam);
            }

            foreach ($data->getTeamInvites() as $dataTeam) {
                $dataTeam->getInvites()->removeElement($data);
                $data->getTeamInvites()->removeElement($dataTeam);
            }

            foreach ($data->getTeamRequests() as $dataTeam) {
                $dataTeam->getRequests()->removeElement($data);
                $data->getTeamRequests()->removeElement($dataTeam);
            }

            $this->getDoctrine()->getManager()->flush();
        }
        $team->getMembers()->add($data);
        $team->getRequests()->removeElement($data);
        $data->getTeamRequests()->removeElement($team);

        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('success', 'Заявка принята');


        return $this->redirect($this->generateUrl('site_account_show'));
    }

    /**
     * @Route("/{team}/decline/{data}", name="team_request_decline")
     * @param \NemesisPlatform\Game\Entity\Team $team
     * @param Participant                            $data
     *
     * @return RedirectResponse
     * @Security("is_granted('decline_request',team)")
     */
    public function declineUserAction(Team $team, Participant $data)
    {
        if ($team->getSeason() !== $data->getSeason()) {
            throw new InvalidArgumentException('Несоответствие сезона команды и участника');
        }

        if (!$team->getRequests()->contains($data)) {
            throw new InvalidArgumentException('Несуществующая заявка');
        }

        $team->getRequests()->removeElement($data);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Заявка отклонена');

        return $this->redirectToRoute('site_account_show');
    }

    /**
     * @Route("/{team}/request", name="team_request_send")
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return RedirectResponse
     * @Security("is_granted('request',team)")
     */
    public function requestAction(Team $team)
    {
        /** @var User $user */
        $user = $this->getUser();

        $seasonData = $user->getParticipation($team->getSeason());
        $team->getRequests()->add($seasonData);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Заявка подана');

        return $this->redirectToRoute('site_account_show');
    }

    /**
     * @Route("/{team}/kick/{data}", name="team_kick")
     * @param \NemesisPlatform\Game\Entity\Team        $team
     * @param \NemesisPlatform\Game\Entity\Participant $data
     *
     * @return RedirectResponse
     * @Security("is_granted('kick',team)")
     */
    public function kickAction(Team $team, Participant $data)
    {
        if (!$team->getMembers()->contains($data)) {
            throw new InvalidArgumentException('Несуществующий участник');
        }

        $team->getMembers()->removeElement($data);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Участник удален из команды');

        return $this->redirectToRoute('site_account_show');
    }

    /**
     * @Route("/{team}/revoke", name="team_request_revoke")
     * @Security("is_granted('revoke_request',team)")
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return RedirectResponse
     */
    public function revokeRequestAction(Team $team)
    {
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->getUser();
        $data = $user->getParticipation($team->getSeason());

        $team->getRequests()->removeElement($data);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Запрос на вступление в команду отозван');

        return $this->redirectToRoute('site_account_show');
    }

    /**
     * @Route("/{team}/leave", name="team_leave")
     * @Security("is_granted('leave',team)")
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return RedirectResponse
     */
    public function leaveAction(Team $team)
    {
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->getUser();
        $data = $user->getParticipation($team->getSeason());

        $team->getMembers()->removeElement($data);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Вы вышли из команды');
        $this->get('security.token_storage')->getToken()->setAuthenticated(false);

        return $this->redirectToRoute('site_account_show');
    }

    /**
     * @Route("/{team}/disband", name="team_disband")
     * @Security("is_granted('disband',team)")
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return RedirectResponse
     */
    public function disbandAction(Team $team)
    {
        foreach ($team->getMembers() as $member) {
            $member->getTeams()->removeElement($team);
        }

        $this->getDoctrine()->getManager()->remove($team);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Команда расформирована');
        $this->get('security.token_storage')->getToken()->setAuthenticated(false);

        return $this->redirectToRoute('site_account_show');
    }

    /**
     * @Route("/{team}/invite", name="team_invite_send")
     * @Security("is_granted('invite',team)")
     * @return Response
     *
     * @param Request                                $request
     * @param \NemesisPlatform\Game\Entity\Team $team
     * @Template()
     */
    public function inviteAction(Request $request, Team $team)
    {
        $form = $this->createFormBuilder()
                     ->add(
                         'user',
                         'entity_autocomplete',
                         [
                             'label'                 => 'ФИО участника',
                             'required'              => false,
                             'class' => Participant::class,
                             'action'                => $this->generateUrl(
                                 'site_user_autocomplete',
                                 ['season' => $team->getSeason()->getId()],
                                 RouterInterface::ABSOLUTE_PATH
                             ),
                             'attr'                  => ['help_text' => 'Начните вводить и выберите из списка автодополнения'],
                             'visible_property_path' => 'toString',
                             'mapped'                => false,

                         ]
                     )
                     ->add('submit', 'submit', ['label' => 'Пригласить'])
                     ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var Participant $member */
            $member = $form->get('user')->getData();

            if ($team->getMembers()->contains($member)) {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    $member->getUser()->getFormattedName().' уже состоит в вашей команде.'
                );

                return $this->redirect(
                    $this->generateUrl('team_invite_send', ['team' => $team->getID()])
                );
            }
            if ($team->getInvites()->contains($member)) {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    $member->getUser()->getFormattedName().' уже приглашен в вашу команду.'
                );

                return $this->redirect(
                    $this->generateUrl('team_invite_send', ['team' => $team->getID()])
                );
            }
            if ($team->getRequests()->contains($member)) {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    $member->getUser()->getFormattedName()
                    .' подал запрос на вступление в вашу команду. Вы можете одобрить его.'
                );

                return $this->redirect(
                    $this->generateUrl('team_invite_send', ['team' => $team->getID()])
                );
            }

            if ($member->isFrozen() || !$team->isAbleToJoin($member->getUser())) {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    $member->getUser()->getFormattedName().' не может изменить свою команду.'
                );

                return $this->redirect(
                    $this->generateUrl('team_invite_send', ['team' => $team->getID()])
                );
            }

            $team->getInvites()->add($member);

            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $member->getUser()->getFormattedName().' приглашен в вашу команду'
            );

            return $this->redirect($this->generateUrl('team_invite_send', ['team' => $team->getID()]));
        }

        return ['team' => $team, 'form' => $form->createView()];
    }

    /**
     * @param \NemesisPlatform\Game\Entity\Team $team
     * @Security("is_granted('revoke_invite',team)")
     * @param Participant                            $data
     *
     * @return RedirectResponse
     * @Route("/{team}/invite/{data}/revoke", name="team_invite_revoke")
     */
    public function revokeInviteAction(Team $team, Participant $data)
    {
        if (!$team->getInvites()->contains($data)) {
            throw new InvalidArgumentException('Несуществующая заявка');
        }

        $team->getInvites()->removeElement($data);
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('success', 'Команда расформирована');

        return $this->redirect($this->generateUrl('site_account_show'));
    }

    /**
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return RedirectResponse
     * @Route("/{team}/invite/accept", name="team_invite_accept")
     * @Security("is_granted('accept_invite',team)")
     */
    public function acceptInviteAction(Team $team)
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = $user->getParticipation($team->getSeason());

        if (!$team->getInvites()->contains($data)) {
            throw new InvalidArgumentException('Несуществующая заявка');
        }

        $single_team = false;
        $season      = $team->getSeason();
        foreach ($season->getRules() as $rule) {
            if ($rule instanceof SingleTeamRule && $rule->isEnabled()) {
                $single_team = true;
            }
        }

        if ($single_team) {
            foreach ($data->getTeams() as $dataTeam) {
                $dataTeam->getMembers()->removeElement($data);
                $data->getTeams()->removeElement($dataTeam);
            }

            foreach ($data->getTeamInvites() as $dataTeam) {
                $dataTeam->getInvites()->removeElement($data);
                $data->getTeamInvites()->removeElement($dataTeam);
            }

            foreach ($data->getTeamRequests() as $dataTeam) {
                $dataTeam->getRequests()->removeElement($data);
                $data->getTeamRequests()->removeElement($dataTeam);
            }

            $this->getDoctrine()->getManager()->flush();
        }

        $team->getMembers()->add($data);
        $team->getInvites()->removeElement($data);
        $data->getTeamInvites()->removeElement($team);

        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Вы приняли приглашение в команду');

        return $this->redirectToRoute('site_account_show');
    }

    /**
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return RedirectResponse
     * @Route("/{team}/invite/reject", name="team_invite_reject")
     */
    public function rejectInviteAction(Team $team)
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = $user->getParticipation($team->getSeason());

        if (!$team->getInvites()->contains($data)) {
            throw new InvalidArgumentException('Несуществующая заявка');
        }

        $team->getInvites()->removeElement($data);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Вы отклонили приглашение в команду');

        return $this->redirectToRoute('site_account_show');
    }

    /**
     * @Route("/season/{season}/datatable", name="site_team_datatable")
     * @param Request $request
     * @param Season  $season
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function datatableAction(Request $request, Season $season)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();
        /** @var TeamRepository $repo */
        $repo = $manager->getRepository(Team::class);
        /** @var array $fields These fields accept sorting and searching */
        $fields = [
            'id'           => 't.id',
            'c_name'       => 't.name',
            'r_name'       => 't.name',
            'captain_name' => 'cu.lastname',
            'captain_id'   => 'cu.id',
            'league'       => 'l.name',
            'tag'          => 't.persistent_tag',
        ];

        $result = $repo->jqueryDataTableFetch(
            $request->query->all(),
            $fields,
            $season->getSite(),
            $season
        );
        /** @var \NemesisPlatform\Game\Entity\Team[] $objects */
        $objects = $result['objects'];
        $output  = $result['output'];

        foreach ($objects as $team) {
            $row                = [];
            $row[]              = $team->getID();
            $row[]              = $team->getCleanName();
            $row[]              = $team->getName();
            $row[]              = $team->getCaptain() ? $team->getCaptain()->getUser()->getLastname() : '';
            $row[]              = $team->getCaptain() ? $team->getCaptain()->getUser()->getID() : '';
            $row[]              = $team->getLeague() ? $team->getLeague()->getName() : '';
            $row[]              = substr($team->getPersistentTag(), 0, 6);
            $output['aaData'][] = $row;
        }

        return new Response(json_encode($output));
    }
}
