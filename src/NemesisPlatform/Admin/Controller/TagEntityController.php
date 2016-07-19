<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.11.2014
 * Time: 12:28
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Core\Account\Entity\Tag;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TagEntityController
 *
 * @package NemesisPlatform\Admin\Controller
 * @Route("/utils")
 */
class TagEntityController extends Controller
{

    /**
     * @Route("/tag/list", name="site_admin_utils_tag_list")
     * @Method("GET")
     * @Template()
     */
    public function listTagsAction()
    {
        $tags   = $this->getDoctrine()->getRepository(Tag::class)->findAll();
        $counts = [];
        foreach ($tags as $tag) {
            $teams = count($this->getDoctrine()->getRepository(Team::class)->findByTag($tag));
            $users = count($this->getDoctrine()->getRepository(User::class)->findByTag($tag));

            $counts[$tag->getId()]['teams'] = $teams;
            $counts[$tag->getId()]['users'] = $users;
        }

        return ['tags' => $tags, 'count' => $counts];
    }

    /**
     * @param \NemesisPlatform\Core\Account\Entity\Tag $tag
     * @Route("/tag/find_team/{tag}", name="site_admin_utils_tag_find_team")
     * @Method("GET")
     * @Template()
     *
     * @return Response
     */
    public function listTaggedTeamsAction(Tag $tag)
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findByTag($tag);

        return ['teams' => $teams, 'tag' => $tag];
    }

    /**
     * @param \NemesisPlatform\Core\Account\Entity\Tag $tag
     * @Route("/tag/find_user/{tag}", name="site_admin_utils_tag_find_user")
     * @Method("GET")
     * @Template()
     *
     * @return Response
     */
    public function listTaggedUsersAction(Tag $tag)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findByTag($tag);

        return ['users' => $users, 'tag' => $tag];
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/tag/tag_user", name="site_admin_utils_tag_user")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function tagUsersAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->add('tag', 'entity', ['class' => Tag::class, 'label' => 'Тэг'])
                     ->add(
                         'user_list',
                         'textarea',
                         [
                             'label' => 'Список ID пользователей',
                             'attr'  => ['help_text' => 'По одному ID на строку'],
                         ]
                     )
                     ->add('submit', 'submit', ['label' => 'Добавить тег'])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $userList = $form->get('user_list')->getData();
            $tag      = $form->get('tag')->getData();

            $total   = 0;
            $updated = 0;
            $skipped = 0;

            foreach (explode(PHP_EOL, $userList) as $userId) {
                $user = $manager->getRepository(User::class)->find($userId);

                if (!$user) {
                    $skipped++;
                    continue;
                }

                if ($user->getTags()->contains($tag)) {
                    $skipped++;
                } else {
                    $updated++;
                    $user->getTags()->add($tag);
                }

                $total++;
            }

            $manager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(
                    'Обработано записей: %d. Обновлено: %d. Пропушено %d (отсутствует или дублирование).',
                    $total,
                    $updated,
                    $skipped
                )
            );

            return $this->redirect($this->generateUrl('site_admin_utils_tag_user'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/tag/tag_team", name="site_admin_utils_tag_team")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function tagTeamsAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->add('tag', 'entity', ['class' => Tag::class, 'label' => 'Тэг'])
                     ->add(
                         'team_list',
                         'textarea',
                         ['label' => 'Список ID команд', 'attr' => ['help_text' => 'По одному ID на строку']]
                     )
                     ->add('submit', 'submit', ['label' => 'Добавить тег'])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $teamList = $form->get('team_list')->getData();
            $tag      = $form->get('tag')->getData();

            $total   = 0;
            $updated = 0;
            $skipped = 0;

            foreach (explode(PHP_EOL, $teamList) as $teamId) {
                $team = $manager->getRepository(Team::class)->find($teamId);
                if ($team->getTags()->contains($tag)) {
                    $skipped++;
                } else {
                    $updated++;
                    $team->getTags()->add($tag);
                }

                $total++;
            }

            $manager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Обработано записей: %d. Обновлено: %d. Дублирование в %d.', $total, $updated, $skipped)
            );

            return $this->redirect($this->generateUrl('site_admin_utils_tag_team'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Team                                          $team
     * @param \NemesisPlatform\Core\Account\Entity\Tag $tag
     *
     * @return RedirectResponse
     *
     * @Route("/tag/{tag}/team/{team}/remove", name="admin_remove_tag_from_team")
     * @Method("GET")
     */
    public function untagTeamAction(Team $team, Tag $tag)
    {
        if ($team->getTags()->contains($tag)) {
            $team->getTags()->removeElement($tag);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('site_admin_utils_tag_find_team', ['tag' => $tag->getId()]);
    }

    /**
     * @param \NemesisPlatform\Core\Account\Entity\User $user
     * @param Tag                                            $tag
     *
     * @return RedirectResponse
     *
     * @Route("/tag/{tag}/user/{user}/remove", name="admin_remove_tag_from_user")
     * @Method("GET")
     */
    public function untagUserAction(User $user, Tag $tag)
    {
        if ($user->getTags()->contains($tag)) {
            $user->getTags()->removeElement($tag);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('site_admin_utils_tag_find_user', ['tag' => $tag->getId()]);
    }
}
