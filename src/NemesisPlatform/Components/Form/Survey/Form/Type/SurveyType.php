<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.05.2015
 * Time: 14:16
 */

namespace NemesisPlatform\Components\Form\Survey\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\MapperAwareInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\TransformerAwareInterface;
use NemesisPlatform\Components\Form\Survey\Entity\Survey;
use NemesisPlatform\Components\Form\Survey\Entity\SurveyResult;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SurveyType extends AbstractType
{
    /** @var  TokenStorageInterface */
    private $tokenStorage;
    /** @var  EntityManagerInterface */
    private $manager;

    /**
     * SurveyType constructor.
     *
     * @param TokenStorageInterface  $tokenStorage
     * @param EntityManagerInterface $manager
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $manager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->manager      = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Survey $survey */
        $survey = $options['survey'];


        $user = $this->tokenStorage->getToken()->getUser();


        $questions = $builder->create(
            'q',
            null,
            [
                'required'       => true,
                'compound'       => true,
                'label'          => false,
                'error_bubbling' => true,
                'mapped'         => false,
            ]
        );

        foreach ($survey->getQuestions() as $question) {
            $field = $question->getField();
            if ($field instanceof FormInjectorInterface) {
                $field->injectForm($questions, []);
            } else {
                $questions->add(
                    $field->getName(),
                    $field->getViewForm(),
                    array_merge($field->getViewFormOptions())
                );

                if ($field instanceof MapperAwareInterface) {
                    $questions->get($field->getName())->setDataMapper($field->getFormMapper());
                }

                if ($field instanceof TransformerAwareInterface) {
                    $questions->get($field->getName())->addModelTransformer($field->getFormTransformer());
                }
            }
        }

        $builder->add($questions);

        $result = new SurveyResult();

        if ($survey->isEditAllowed()) {
            $savedResult = $this
                ->manager
                ->getRepository(SurveyResult::class)
                ->findOneBy(
                    [
                        'author' => $user,
                        'survey' => $survey,
                    ]
                );
            $result      = $savedResult ?: $result;
        }


        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($survey, $user) {

                $form = $event->getForm();

                /** @var SurveyResult $result */
                $result = $event->getData();
                $result->setSurvey($survey);

                if ($user instanceof UserInterface) {
                    $result->setAuthor($user);
                }

                $result->sanitize();

                foreach ($result->getAnswers() as $answer) {
                    $form->get('q')->get($answer->getValue()->getField()->getName())->setData($answer->getValue());
                }
            }
        );

        $builder->setData($result);

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($survey, $user) {

                $form = $event->getForm();

                /** @var SurveyResult $result */
                $result = $event->getData();
                $result->setSurvey($survey);
                if ($user instanceof UserInterface) {
                    $result->setAuthor($user);
                }

                $result->setTimeUpdated(new \DateTime());

                foreach ($form->get('q')->all() as $key => $value) {
                    if (!$value->getData()) {
                        continue;
                    }

                    dump($value);

//                    $answer = new SurveyAnswer($value->getData());
//                    $result->addAnswer($answer);
                }
            }
        );


    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['survey'] = $options['survey'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => SurveyResult::class]);
        $resolver->setRequired(['survey']);
        $resolver->setAllowedTypes(['survey' => Survey::class]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'survey';
    }
}
