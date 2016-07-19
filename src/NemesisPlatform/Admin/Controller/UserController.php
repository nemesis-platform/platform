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
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Season;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package NemesisPlatform\Admin\Controller\Users
 * @Route("/user")
 */
class UserController extends Controller
{
    private static $datatablesFields = [
        'id'     => 'u.id',
        'fio'    => 'CONCAT(CONCAT(CONCAT(CONCAT(u.lastname,\' \'),u.firstname),\' \'),u.middlename)',
        'email'  => 'u.email',
        'date'   => 'u.registerDate',
        'status' => 'u.status',
        'phone'  => 'p.phonenumber',
    ];

    /**
     * @param Request $request
     *
     * @return Response|array
     * @Route("/list", name="site_admin_user_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $season = null;
        if ($request->get('season', null)) {
            $season = $this->getDoctrine()->getRepository(Season::class)->find($request->get('season', null));
        }

        $sites = $this->getDoctrine()->getRepository(SiteInterface::class)->findAll();

        return ['season' => $season, 'sites' => $sites];
    }


    /**
     * @Route("/{user}/edit", name="site_admin_user_edit")
     * @Template()
     * @param Request $request
     * @param User    $user
     *
     * @return Response|array
     */
    public function editAction(Request $request, User $user)
    {
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm('user_type', $user)
            ->add('submit', 'submit', ['label' => 'Обновить пользователя']);


        $form->handleRequest($request);
        if ($form->isValid()) {
            $manager->flush();

            return $this->redirect(
                $this->generateUrl('site_admin_user_edit', ['user' => $user->getID()])
            );
        }

        return ['form' => $form->createView(), 'user' => $user];
    }

    /**
     * @Route("/datatable", name="site_admin_user_datatable")
     * @param Request $request
     *
     * @return Response
     */
    public function datatableAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $repo = $manager->getRepository(User::class);
        /** @var array $fields These fields accept sorting and searching */
        $fields = self::$datatablesFields;

        $result = $repo->jqueryDataTableFetch(
            $request->query->all(),
            $fields,
            false
        );
        /** @var User[] $objects */
        $objects = $result['objects'];
        $output  = $result['output'];

        foreach ($objects as $user) {
            $output['aaData'][] = $this->createDatatablesRow($user);
        }

        return new JsonResponse($output);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    private function createDatatablesRow(User $user)
    {
        $row   = [];
        $row[] = $user->getID();
        $row[] = $user->getFormattedName('%l %f %m');
        $row[] = $user->getEmail();
        $row[] = $user->getRegisterDate()->format('Y.m.d H:i:s');
        $row[] = $user->getStatus();
        $row[] = $user->getPhone() ? $user->getPhone()->getFullPhoneNumber() : null;

        return $row;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/autocomplete", name="site_admin_user_autocomplete")
     */
    public function autocompleteAction(Request $request)
    {
        if (!$request->query->get('term')) {
            return new JsonResponse([]);
        }

        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();
        $query   = $manager->getRepository(User::class)->createQueryBuilder('u');

        $query->orWhere('u.email LIKE :term')->setParameter('term', '%'.$request->query->get('term').'%');
        $query->orWhere('u.firstname LIKE :term')->setParameter('term', '%'.$request->query->get('term').'%');
        $query->orWhere('u.lastname LIKE :term')->setParameter('term', '%'.$request->query->get('term').'%');
        $query->orWhere('u.middlename LIKE :term')->setParameter('term', '%'.$request->query->get('term').'%');

        return new JsonResponse(
            array_map(
                function (User $user) {
                    return [
                        'label' =>
                            sprintf(
                                "%s (%s)",
                                $user->getFormattedName(),
                                $user->getEmail()
                            ),
                        'id'    => $user->getID(),
                    ];
                },
                $query->getQuery()->getResult()
            )
        );
    }
}
