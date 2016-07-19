<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.05.2015
 * Time: 14:48
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Admin\Form\Type\CertificateTypeType;
use NemesisPlatform\Game\Entity\Certificate\CertificateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CertificateTypeController
 *
 * @package NemesisPlatform\Admin\Controller
 * @Route("/certificate/types")
 */
class CertificateTypeController extends Controller
{
    /**
     * @Route("/list", name="admin_certificates_types_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        $types = $this->getDoctrine()->getManager()->getRepository(CertificateType::class)->findAll();

        return ['types' => $types];
    }

    /**
     * @Route("/create", name="admin_certificates_types_create")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CertificateTypeType(), new CertificateType());
        $form->add('submit', 'submit', ['label' => 'Создать']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->persist($form->getData());
            $manager->flush();

            return $this->redirectToRoute('admin_certificates_types_list');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{type}/edit", name="admin_certificates_types_edit")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @param Request         $request
     * @param CertificateType $type
     *
     * @return Response
     */
    public function editAction(Request $request, CertificateType $type)
    {
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CertificateTypeType(), $type);
        $form->add('submit', 'submit', ['label' => 'Обновить']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('admin_certificates_types_list');
        }

        return ['form' => $form->createView(), 'type' => $type];
    }

    /**
     * @Route("/{type}/delete", name="admin_certificates_types_delete")
     * @Method("GET")
     * @param CertificateType $type
     *
     * @return RedirectResponse
     */
    public function deleteAction(CertificateType $type)
    {
        $manager = $this->getDoctrine()->getManager();

        foreach ($type->getCertificates() as $cert) {
            $manager->remove($cert);
        }

        $manager->remove($type);
        $manager->flush();

        return $this->redirectToRoute('admin_certificates_types_list');
    }
}
