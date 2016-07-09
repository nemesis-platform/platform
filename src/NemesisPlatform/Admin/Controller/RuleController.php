<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 25.02.2015
 * Time: 16:02
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Components\Form\FormTypedInterface;
use NemesisPlatform\Game\Entity\Rule\AbstractRuleEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RuleController
 *
 * @package NemesisPlatform\Admin\Controller
 * @Route("/rules")
 */
class RuleController extends Controller
{
    /**
     * @Template()
     * @Route("/", name="admin_rule_list")
     */
    public function listAction()
    {
        $rules = $this->getDoctrine()->getRepository(AbstractRuleEntity::class)->findAll();

        return ['rules' => $rules];
    }

    /**
     * @Template()
     * @Route("/types", name="admin_rule_list_types")
     */
    public function listTypesAction()
    {
        return ['types' => $this->get('nemesis.rule_registry')->all()];
    }

    /**
     * @Template()
     * @Route("/create/{type}", name="admin_rule_create_typed_instance")
     * @param Request $request
     * @param         $type
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request, $type)
    {
        if (!$this->get('nemesis.rule_registry')->has($type)) {
            throw new NotFoundHttpException("Type $type is not registered as rule type");
        }

        $rule = $this->get('nemesis.rule_registry')->get($type);

        if ($rule instanceof FormTypedInterface) {
            $formType = $rule->getFormType();
            $form     = $this->createForm($formType, clone $rule, ['data_class' => get_class($rule)]);
        } else {
            $form = $this->createFormBuilder(clone $rule, ['data_class' => get_class($rule)])->getForm();
        }

        $form->add('submit', 'submit');

        $form->handleRequest($request);
        if ($form->isValid()) {
            $rule = $form->getData();

            $this->getDoctrine()->getManager()->persist($rule);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_rule_edit_instance', ['rule' => $rule->getId()]);
        }

        return ['form' => $form->createView(), 'rule' => $this->get('nemesis.rule_registry')->get($type)];
    }

    /**
     * @Template()
     * @Route("/{rule}/edit", name="admin_rule_edit_instance")
     * @param Request            $request
     * @param AbstractRuleEntity $rule
     *
     * @return Response
     */
    public function editAction(Request $request, AbstractRuleEntity $rule)
    {
        $form = $this->createForm($rule->getFormType(), $rule)
                     ->add('submit', 'submit');

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var AbstractRuleEntity $rule */
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_rule_edit_instance', ['rule' => $rule->getId()]);
        }

        return ['form' => $form->createView(), 'rule' => $rule];
    }

    public function deleteAction(AbstractRuleEntity $rule)
    {
    }
}
