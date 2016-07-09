<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.06.2015
 * Time: 9:37
 */

namespace NemesisPlatform\Modules\Game\Core\Form\Transformer;

use NemesisPlatform\Modules\Game\Core\Entity\ArrayDecision\ArrayDecision;
use NemesisPlatform\Modules\Game\Core\Entity\ArrayDecision\ArrayDecisionDataRecord;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DecisionRoundInterface;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayDecisionTransformer implements DataTransformerInterface
{
    /** @var  DecisionRoundInterface */
    private $round;
    /** @var  \NemesisPlatform\Game\Entity\Team */
    private $team;
    /** @var  \NemesisPlatform\Core\Account\Entity\User */
    private $user;

    /**
     * ArrayDecisionTransformer constructor.
     *
     * @param DecisionRoundInterface                         $round
     * @param Team                                           $team
     * @param \NemesisPlatform\Core\Account\Entity\User $user
     */
    public function __construct(DecisionRoundInterface $round, Team $team, User $user)
    {
        $this->round = $round;
        $this->team  = $team;
        $this->user  = $user;
    }


    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        /** @var ArrayDecision $decision */
        $decision = $this->round->createDecision();
        $decision->setSubmissionTime(new \DateTime());
        $decision->setTeam($this->team);
        $decision->setAuthor($this->user);

        $value = (array)$value;
        foreach ($value as $key => $data) {
            if ($data === null) {
                continue;
            }

            $dr = new ArrayDecisionDataRecord();
            $dr->setKey($key);
            $dr->setValue($data ?: 0);

            $decision->addData($dr);
        }

        return $decision;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!$value instanceof ArrayDecision) {
            throw new TransformationFailedException('value should be instance of ArrayDecision');
        }

        return $value;
    }
}
