<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 08.10.2014
 * Time: 11:36
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Core\Account\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TagController
 *
 * @package NemesisPlatform\Admin\Controller
 * @Route("/tags")
 */
class TagController extends Controller
{
    /**
     * @param Request $request
     * @param Tag     $tag
     *
     * @return Response
     * @Template()
     * @Route("/{tag}/edit", name="tag_edit")
     * @Method({"GET","POST"})
     */
    public function editAction(Request $request, Tag $tag)
    {
        $form = $this->createForm('tag', $tag)
            ->add('submit', 'submit', ['label' => 'Обновить']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Успешное обновление тега');

            return $this->redirect($this->generateUrl('site_admin_utils_tag_list'));
        }

        return ['form' => $form->createView(), 'tag' => $tag];
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Template()
     * @Route("/create", name="tag_create")
     * @Method({"GET","POST"})
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm('tag')
            ->add('submit', 'submit', ['label' => 'Создать']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $tag = $form->getData();
            $this->getDoctrine()->getManager()->persist($tag);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Успешное создание тега');

            return $this->redirect($this->generateUrl('site_admin_utils_tag_list'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Tag $tag
     *
     * @return Response
     * @Route("/{tag}/delete", name="tag_delete")
     * @Method("GET")
     */
    public function deleteAction(Tag $tag)
    {
        $this->getDoctrine()->getManager()->remove($tag);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('site_admin_utils_tag_list'));
    }
}
