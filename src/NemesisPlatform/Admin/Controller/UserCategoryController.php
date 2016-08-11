<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.09.2014
 * Time: 16:32
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Game\Entity\League;
use NemesisPlatform\Game\Entity\UserCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserCategoryController
 *
 * @package NemesisPlatform\Admin\Controller
 * @Route("/categories")
 */
class UserCategoryController extends Controller
{

    /**
     * @return Response
     * @Route("/list", name="site_admin_usercategory_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        /** @var \NemesisPlatform\Game\Entity\SeasonedSite $site */
        $site = $this->get('site.provider')->getSite();
        /** @var League[] $leagues */
        $leagues = [];
        foreach ($site->getSeasons() as $season) {
            $leagues = array_merge($leagues, $season->getLeagues()->toArray());
        }

        /** @var \NemesisPlatform\Game\Entity\UserCategory[] $categories */
        $categories = [];
        foreach ($leagues as $league) {
            $categories = array_merge($categories, $league->getCategories()->toArray());
        }

        return ['categories' => $categories];
    }

    /**
     * @return Response
     *
     * @param Request                                   $request
     * @param \NemesisPlatform\Game\Entity\UserCategory $category
     * @Route("/{category}/edit", name="site_admin_usercategory_edit")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function editAction(Request $request, UserCategory $category)
    {
        $form = $this
            ->createForm('user_category_type', $category)
            ->add(
                'fields',
                'collection',
                [
                    'type'         => 'entity',
                    'label'        => 'Поля',
                    'allow_add'    => 'true',
                    'allow_delete' => 'true',
                    'options'      => [
                        'class' => AbstractField::class,
                    ],
                ]
            )
            ->add('submit', 'submit', ['label' => 'Сохранить категорию']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Категория сохранена');

            return $this->redirect(
                $this->generateUrl(
                    'site_admin_usercategory_edit',
                    ['category' => $category->getId()]
                )
            );
        }

        return ['form' => $form->createView()];
    }
}
