<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.02.2015
 * Time: 14:16
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Admin\Service\Generator\EntityGeneratorInterface;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Form\FormTypedInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GeneratorController extends Controller
{
    /**
     * @Route("/utils/generators/list", name="admin_generators_list")
     * @Template()
     */
    public function listAction()
    {
        $generators = $this->get('nemesis.generator_registry')->all();

        return ['generators' => $generators];
    }

    /**
     * @param Request $request
     * @param         $type
     *
     * @Route("/utils/generators/{type}/generate", name="admin_generators_form")
     *
     * @return Response
     */
    public function formAction(Request $request, $type)
    {
        $generator = $this->get('nemesis.generator_registry')->get($type);

        /** @var EntityGeneratorInterface|FormTypedInterface|FormInjectorInterface $generator */
        if ($generator instanceof FormInjectorInterface) {
            $builder = $this->createFormBuilder(
                null,
                [
                    'action' => $this->generateUrl('admin_generators_form', ['type' => $generator->getType()]),
                ]
            );
            $generator->injectForm($builder);
            $form = $builder->getForm();
        } elseif ($generator instanceof FormTypedInterface) {
            $form = $this->createForm($generator->getFormType());
        } else {
            $form = $this->createFormBuilder()->getForm();
        }

        $form->add('submit', 'submit');

        $form->handleRequest($request);
        if ($form->isValid()) {
            $report = $generator->generate($form->getData());

            return $this->render('NemesisAdminBundle:Generator:report.html.twig', ['report' => $report]);
        }

        return $this->render('NemesisAdminBundle:Generator:form.html.twig', ['form' => $form->createView()]);
    }
}
