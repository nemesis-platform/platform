<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.10.2014
 * Time: 11:48
 */

namespace NemesisPlatform\Components\ExportImport\Controller;

use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Form\FormTypedInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ExporterController
 *
 * @package NemesisPlatform\Components\ExportImport\Controller
 * @Route("/exporters")
 */
class ExporterController extends Controller
{
    /**
     * @Template()
     * @Route("/list", name="exporters_list")
     * @Method("GET")
     */
    public function listAction()
    {
        return ['exporters' => $this->get('exporter.registry')->all()];
    }

    /**
     * @Route("/{type}/export", name="exporters_form")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param         $type
     *
     * @return Response
     */
    public function formAction(Request $request, $type)
    {
        $registry = $this->get('exporter.registry');

        if (!$registry->has($type)) {
            throw new NotFoundHttpException('Exporter not found');
        }

        $exporter = $registry->get($type);
        $builder  = $this->createFormBuilder(
            null,
            ['action' => $this->generateUrl('exporters_form', ['type' => $exporter->getType()])]
        );

        if ($exporter instanceof FormTypedInterface) {
            $builder->add($exporter->getFormType());
        }
        if ($exporter instanceof FormInjectorInterface) {
            $exporter->injectForm($builder);
        }

        $builder->add('submit', 'submit', ['label' => 'exporters.do_export']);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $exporter->export($form->getData());
        }

        return ['exporter' => $exporter, 'form' => $form->createView()];
    }

}
