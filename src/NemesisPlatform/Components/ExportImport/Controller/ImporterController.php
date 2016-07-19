<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.03.2015
 * Time: 15:37
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
 * Class ImporterController
 *
 * @package NemesisPlatform\Components\ExportImport\Controller
 * @Route("/importers")
 */
class ImporterController extends Controller
{
    /**
     * @Template()
     * @Route("/list", name="importers_list")
     * @Method("GET")
     */
    public function listAction()
    {
        return ['importers' => $this->get('importer.registry')->all()];
    }

    /**
     * @Route("/{type}/import", name="importers_form")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param         $type
     *
     * @return Response
     */
    public function formAction(Request $request, $type)
    {
        $registry = $this->get('importer.registry');

        if (!$registry->has($type)) {
            throw new NotFoundHttpException('Importer not found');
        }

        $importer = $registry->get($type);
        $builder  = $this->createFormBuilder(
            null,
            ['action' => $this->generateUrl('importers_form', ['type' => $importer->getType()])]
        );

        if ($importer instanceof FormTypedInterface) {
            $builder->add($importer->getFormType());
        }
        if ($importer instanceof FormInjectorInterface) {
            $importer->injectForm($builder);
        }

        $builder->add('submit', 'submit', ['label' => 'importers.do_import']);
        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $importer->setup($form->getData());

            if (!$importer->isValid()) {
                throw new \InvalidArgumentException('Data submitted to importer is not valid');
            }

            $result = $importer->getResult();

            foreach ($importer->getPostProcessors() as $processor) {
                if ($processor->isApplicable($result)) {
                    $result->process($processor);
                }
            }

            return $this->render('ImportBundle:Importer:report.html.twig', ['report' => $result->getReport()]);
        }

        return ['importer' => $importer, 'form' => $form->createView()];
    }
}
