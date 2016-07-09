<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 27.05.2015
 * Time: 12:32
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Form\FormTypedInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FormController
 *
 * @Route("/fields")
 */
class FieldsController extends Controller
{
    /**
     * @Template()
     * @Route("/list", name="storable_forms_field_list")
     * @return Response
     */
    public function listAction()
    {
        $fields = $this->getDoctrine()->getRepository(AbstractField::class)->findAll();

        return ['fields' => $fields];
    }

    /**
     * @Template()
     * @Route("/types", name="storable_forms_field_list_types")
     * @return Response
     */
    public function listTypesAction()
    {
        return ['types' => $this->get('scaytrase.stored_forms.fields_registry')->all()];
    }


    /**
     * @param Request       $request
     * @param AbstractField $field
     * @Route("/{field}/edit", name="storable_forms_field_edit")
     *
     * @return Response
     * @Template()
     */
    public function editAction(Request $request, AbstractField $field)
    {
        if ($field instanceof FormInjectorInterface) {
            $builder = $this->createFormBuilder();
            $field->injectForm($builder);
            $form = $builder->getForm();
        } elseif ($field instanceof FormTypedInterface) {
            $form = $this->createForm($field->getFormType());
        } else {
            $form = $this->createForm('form');
        }

        $form->setData($field);

        $form->add('submit', 'submit', ['label' => 'Обновить']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Поле успешно сохранено');

            return $this->redirect(
                $this->generateUrl('storable_forms_field_edit', ['field' => $field->getId()])
            );
        }

        /** @var FormInjectorInterface|FormTypedInterface|AbstractField $field */

        return ['form' => $form->createView(), 'field' => $field];
    }

    /**
     * @param Request $request
     * @Route("/{type}/create", name="storable_forms_field_create")
     * @Template()
     *
     * @param         $type
     *
     * @return Response
     */
    public function createAction(Request $request, $type)
    {
        $registry = $this->get('scaytrase.stored_forms.fields_registry');
        if (!$registry->has($type)) {
            throw new NotFoundHttpException('Field type not found');
        }

        /** @var AbstractField|FormInjectorInterface|FormTypedInterface $field */
        $field = clone $registry->get($type);

        if ($field instanceof FormInjectorInterface) {
            $builder = $this->createFormBuilder();
            $field->injectForm($builder);
            $form = $builder->getForm();
        } elseif ($field instanceof FormTypedInterface) {
            $form = $this->createForm($field->getFormType());
        } else {
            $form = $this->createForm('form');
        }

        $form->setData($field);

        $form->add('submit', 'submit', ['label' => 'Создать']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($field);
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Поле успешно создано');

            return $this->redirect(
                $this->generateUrl('storable_forms_field_edit', ['field' => $field->getId()])
            );
        }

        return ['form' => $form->createView(), 'type' => $type];
    }

    /**
     * @param AbstractField $field
     *
     * @return RedirectResponse
     * @Route("/{field}/delete", name="storable_forms_field_delete")
     */
    public function deleteAction(AbstractField $field)
    {
        $this->getDoctrine()->getManager()->remove($field);
        $this->getDoctrine()->getManager()->flush();
        $this->get('session')->getFlashBag()->add('success', 'Поле успешно удалено');

        return $this->redirect($this->generateUrl('storable_forms_field_list'));
    }
}
