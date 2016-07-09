<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 03.07.2015
 * Time: 13:59
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Core\CMS\Entity\Block\AbstractBlock;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BlocksController
 *
 * @package NemesisPlatform\Admin\Controller
 * @Route("/blocks")
 */
class BlocksController extends Controller
{
    /**
     * @Route("/list_types", name="admin_blocks_list_types")
     * @Template()
     * @return Response
     */
    public function listTypesAction()
    {
        return ['blocks' => $this->get('nemesis.block_registry')->all()];
    }

    /**
     * @Route("/list", name="admin_blocks_list")
     * @Template()
     * @return Response
     */
    public function listAction()
    {
        $blocks = $this->getDoctrine()->getRepository(AbstractBlock::class)->findAll();

        return ['blocks' => $blocks];
    }

    /**
     * @Route("/{type}/create", name="admin_blocks_create")
     * @Template()
     * @param Request $request
     * @param         $type
     *
     * @return Response
     */
    public function createAction(Request $request, $type)
    {
        $registry = $this->get('nemesis.block_registry');

        if (!$registry->has($type)) {
            throw new NotFoundHttpException('block type not found');
        }

        /** @var AbstractBlock $block */
        $block = $registry->get($type);

        $form = $this->createForm($block->getFormType(), $block)
                     ->add('submit', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $block = $form->getData();

            $this->getDoctrine()->getManager()->persist($block);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_blocks_list');
        }

        return ['type' => $type, 'form' => $form->createView()];
    }

    /**
     * @Route("/{block}/edit", name="admin_blocks_edit")
     * @Template()
     * @param Request $request
     * @param         $type
     *
     * @return Response
     */
    public function editAction(Request $request, AbstractBlock $block)
    {
        $form = $this->createForm($block->getFormType(), $block)
                     ->add('submit', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $block = $form->getData();

            $this->getDoctrine()->getManager()->persist($block);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_blocks_list');
        }

        return ['form' => $form->createView(), 'block' => $block];
    }
}
