<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.01.2015
 * Time: 15:24
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Form\FormTypedInterface;
use NemesisPlatform\Components\Themes\Entity\ThemeInstance;
use NemesisPlatform\Components\Themes\Service\CompilableThemeInterface;
use NemesisPlatform\Components\Themes\Service\ConfigurableThemeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThemeInstanceController
 *
 * @Route("/themes")
 */
class ThemeInstanceController extends Controller
{
    /**
     * @param Request $request
     * @Route("/create", name="switchable_theme_instance_create")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm('switchable_theme_instance')->add('submit', 'submit');


        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var ThemeInstance $instance */
            $instance = $form->getData();
            $instance->setConfig([]);

            $this->getDoctrine()->getManager()->persist($instance);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Тема успешно создана');

            return $this->redirectToRoute('switchable_theme_instance_edit', ['instance' => $instance->getId()]);
        }

        return ['form' => $form->createView()];
    }

    /**
     * @return Response
     * @Route("/list", name="switchable_theme_instance_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        /** @var ThemeInstance[] $instances */
        $instances = $this
            ->getDoctrine()->getManager()->getRepository(ThemeInstance::class)->findAll();

        return ['themes' => $instances];
    }

    /**
     * @param ThemeInstance $instance
     *
     * @Route("/{instance}/delete", name="switchable_theme_instance_delete")
     * @Method("GET")
     * @return RedirectResponse
     */
    public function deleteAction(ThemeInstance $instance)
    {
        $this->getDoctrine()->getManager()->remove($instance);
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('warning', 'Тема успешно удалена');

        return $this->redirectToRoute('switchable_theme_instance_list');
    }

    /**
     * @param ThemeInstance $instance
     *
     * @Route("/{instance}/regenerate", name="switchable_theme_instance_regenerate")
     * @Method("GET")
     * @return RedirectResponse
     */
    public function regenerateAction(ThemeInstance $instance)
    {
        $theme = $this->get('scaytrase.theme_registry')->get($instance->getTheme());

        if ($theme instanceof ConfigurableThemeInterface) {
            $theme->setConfiguration($instance->getConfig());
        }

        if ($theme instanceof CompilableThemeInterface) {
            $theme->compile();
        }

        return $this->redirectToRoute('switchable_theme_instance_list');
    }

    /**
     * @param Request $request
     * @param ThemeInstance $instance
     * @Route("/{instance}/edit", name="switchable_theme_instance_edit")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @return Response
     */
    public function editAction(Request $request, ThemeInstance $instance)
    {
        $form = $this
            ->createForm('switchable_theme_instance', $instance)
            ->add('submit', 'submit');

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Тема успешно отредактирована');

            return $this->redirectToRoute('switchable_theme_instance_edit', ['instance' => $instance->getId()]);
        }

        return ['form' => $form->createView(), 'instance' => $instance];
    }

    /**
     * @param Request $request
     * @param ThemeInstance $instance
     * @Route("/{instance}/configure", name="switchable_theme_instance_configure")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @return Response
     */
    public function configureAction(Request $request, ThemeInstance $instance)
    {
        $builder = $this->createFormBuilder($instance);

        $theme = $this->get('scaytrase.theme_registry')->get($instance->getTheme());

        $config = $builder->create('config', 'form');
        if ($theme instanceof FormTypedInterface) {
            $config->add($theme->getFormType());
        }
        if ($theme instanceof FormInjectorInterface) {
            $theme->injectForm($config);
        }

        $builder->add($config);

        $form = $builder->getForm();
        $form->add('submit', 'submit');

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Тема успешно сконфигурирована');

            return $this->redirectToRoute(
                'switchable_theme_instance_configure',
                ['instance' => $instance->getId()]
            );
        }

        return ['form' => $form->createView(), 'instance' => $instance];
    }

    /**
     * @param ThemeInstance $instance
     * @Route("/{instance}/clone", name="switchable_theme_instance_clone")
     * @Method("GET")
     *
     * @return RedirectResponse
     */
    public function cloneAction(ThemeInstance $instance)
    {
        $clone = clone $instance;

        $this->getDoctrine()->getManager()->persist($clone);
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('success', 'Тема успешно склонирована');

        return $this->redirectToRoute('switchable_theme_instance_list');
    }
}
