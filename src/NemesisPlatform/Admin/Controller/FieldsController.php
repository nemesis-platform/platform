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
use NemesisPlatform\Components\Form\PersistentForms\Entity\ConfigurableFieldInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\FieldInterface;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\FieldConfigurationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Method("GET")
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
     * @Method("GET")
     * @return Response
     */
    public function listTypesAction()
    {
        return ['types' => $this->get('scaytrase.stored_forms.fields_registry')->all()];
    }


    /**
     * @Route("/{field}/edit", name="storable_forms_field_edit")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @param Request                                                 $request
     * @param AbstractField|FieldInterface|ConfigurableFieldInterface $field
     *
     * @return RedirectResponse|array
     */
    public function editAction(Request $request, AbstractField $field)
    {
        if ($field instanceof ConfigurableFieldInterface) {
            $form = $this->createForm($field->getConfigurationForm(), $field, $field->getConfigurationFormOptions());
        } else {
            $form = $this->createForm(FieldConfigurationType::class);
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


        return ['form' => $form->createView(), 'field' => $field, 'class' => get_class($field)];
    }

    /**
     * @Route("/{type}/create", name="storable_forms_field_create")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @param Request $request
     * @param string  $type
     *
     * @return array|RedirectResponse
     */
    public function createAction(Request $request, $type)
    {
        $registry = $this->get('scaytrase.stored_forms.fields_registry');
        if (!$registry->has($type)) {
            throw new NotFoundHttpException('Field type not found');
        }

        $fieldType = $registry->get($type);

        /** @var AbstractField|FormInjectorInterface|FormTypedInterface $field */
        $field = $fieldType::create();

        if ($field instanceof ConfigurableFieldInterface) {
            $form = $this->createForm($field->getConfigurationForm(), $field, $field->getConfigurationFormOptions());
        } else {
            $form = $this->createForm(FieldConfigurationType::class);
        }

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

        return ['form' => $form->createView(), 'type' => $type, 'alias' => $type];
    }

    /**
     * @param AbstractField $field
     *
     * @return RedirectResponse
     * @Route("/{field}/delete", name="storable_forms_field_delete")
     * @Method("GET")
     */
    public function deleteAction(AbstractField $field)
    {
        $this->getDoctrine()->getManager()->remove($field);
        $this->getDoctrine()->getManager()->flush();
        $this->get('session')->getFlashBag()->add('success', 'Поле успешно удалено');

        return $this->redirect($this->generateUrl('storable_forms_field_list'));
    }
}
