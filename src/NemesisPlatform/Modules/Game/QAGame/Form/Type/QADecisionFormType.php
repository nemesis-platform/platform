<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.06.2015
 * Time: 17:36
 */

namespace NemesisPlatform\Modules\Game\QAGame\Form\Type;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Modules\Game\Core\Form\Type\AbstractDecisionFormType;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Modules\Game\QAGame\Entity\DecisionAnswer;
use NemesisPlatform\Modules\Game\QAGame\Entity\QADecision;
use NemesisPlatform\Modules\Game\QAGame\Entity\QARound;
use NemesisPlatform\Modules\Game\QAGame\Entity\QuestionList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QADecisionFormType extends AbstractDecisionFormType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver); // TODO: Change the autogenerated stub
        $resolver->setDefaults(
            [
                'required'       => true,
                'mapped'         => false,
                'compound'       => true,
                'label'          => false,
                'error_bubbling' => true,
            ]
        );
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $round = $options['round'];
        if (!$round instanceof QARound) {
            throw new \LogicException('QA Decision form belongs to non QA round');
        }

        /** @var Team $team */
        $team = $options['team'];

        /** @var QuestionList $questionList */
        $questionList = $round->getQuestionList();


        $user = $this->tokenStorage->getToken()->getUser();

        foreach ($questionList->getQuestions() as $question) {
            $question->getField()->buildForm($builder, ['mapped' => false]);
        }

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($questionList, $user) {

                $form = $event->getForm();

                /** @var QADecision $result */
                $result = $event->getData();

                if (!$result) {
                    return;
                }

                $result->setQuestionList($questionList);

                $result->setAuthor($user);
                $result->sanitize();

                foreach ($result->getAnswers() as $answer) {
                    $name = $answer->getValue()->getField()->getName();
                    if ($form->has($name)) {
                        $form->get($name)->setData($answer->getValue());
                    }
                }
            }
        );

        /** @var EntityRepository $decisionRepository */
        $decisionRepository = $this->manager->getRepository(QADecision::class);
        /** @var QADecision $result */
        $result = $decisionRepository
            ->findOneBy(['round' => $round, 'team' => $team], ['submissionTime' => 'DESC']);

        $builder->setData(new QADecision($questionList));

        if ($result) {
            foreach ($result->getAnswers() as $answer) {
                $name = $answer->getValue()->getField()->getName();
                if ($builder->has($name)) {
                    $builder->get($name)->setData($answer->getValue());
                }
            }
        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($questionList, $user, $team, $round) {

                $form = $event->getForm();

                /** @var QADecision $result */
                $result = $event->getData();

                if (!$result) {
                    return;
                }

                $result->setQuestionList($questionList);
                $result->setAuthor($user);
                $result->setRound($round);
                $result->setTeam($team);
                $result->setSubmissionTime(new \DateTime());
                $result->sanitize();

                foreach ($form->all() as $key => $value) {
                    if (!$value->getData()) {
                        continue;
                    }

                    $answer = new DecisionAnswer($value->getData());
                    $result->addAnswer($answer);
                }
            }
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options); // TODO: Change the autogenerated stub

        $round = $options['round'];
        if (!$round instanceof QARound) {
            throw new \LogicException('QA Decision form belongs to non QA round');
        }
        /** @var Team $team */
        $team = $options['team'];

        /** @var QuestionList $questionList */
        $questionList = $round->getQuestionList();

        /** @var EntityRepository $decisionRepository */
        $decisionRepository = $this->manager->getRepository(QADecision::class);
        /** @var QADecision $result */
        $result = $decisionRepository
            ->findOneBy(['round' => $round, 'team' => $team], ['submissionTime' => 'DESC']);

        $view->vars['question_list']   = $questionList;
        $view->vars['last_submission'] = $result;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'qa_decision';
    }
}
