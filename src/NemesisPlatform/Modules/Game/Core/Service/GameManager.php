<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 09.07.2015
 * Time: 14:19
 */

namespace NemesisPlatform\Modules\Game\Core\Service;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\MultiSite\Service\SiteProviderInterface;
use NemesisPlatform\Modules\Game\Core\Entity\RatingRecord;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DecisionRoundInterface;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DraftRound;
use NemesisPlatform\Modules\Game\Core\Entity\Round\FilteredRoundInterface;
use NemesisPlatform\Modules\Game\Core\Entity\Round\PeriodicRound;
use NemesisPlatform\Modules\Game\Core\Entity\Round\Round;
use NemesisPlatform\Modules\Game\Core\Entity\Round\TimedRoundInterface;
use NemesisPlatform\Modules\Game\Core\Security\DecisionVoter;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\SeasonedSite;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GameManager
{
    /** @var  SiteProviderInterface */
    private $siteManager;
    /** @var  TokenStorageInterface */
    private $tokenStorage;
    /** @var  EntityManagerInterface */
    private $manager;
    /** @var  AuthorizationCheckerInterface */
    private $authChecker;

    /**
     * GameManager constructor.
     *
*@param EntityManagerInterface              $manager
     * @param SiteProviderInterface         $siteManager
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(
        EntityManagerInterface $manager,
        SiteProviderInterface $siteManager,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authChecker
    ) {
        $this->manager = $manager;
        $this->siteManager = $siteManager;
        $this->tokenStorage = $tokenStorage;
        $this->authChecker = $authChecker;
    }

    /**
     * @param Round $round
     * @param Team $team
     * @return array
     * @throws NotFoundHttpException
     */
    public function getActiveRoundOptions(Round $round = null, Team $team = null)
    {
        return $this->handleRoundOptions($round, $team);
    }

    protected function handleRoundOptions(Round $round = null, Team $team = null)
    {
        /** @var User $user */
        $user = $user = $this->tokenStorage->getToken()->getUser();
        if (!($user instanceof UserInterface)) {
            throw new AccessDeniedHttpException('decision.not_participating');
        }

        /** @var SeasonedSite $site */
        $site = $this->siteManager->getSite();

        if (!$this->authChecker->isGranted(DecisionVoter::MAKE_DECISION, $site)) {
            throw new AccessDeniedHttpException('decision.not_participating');
        }

        $season = $site->getActiveSeason();

        if (!$season) {
            throw new AccessDeniedHttpException('decision.no_season');
        }

        $participant = $user->getParticipation($season);

        if ($team && !($participant->getTeams()->contains($team))) {
            throw new AccessDeniedHttpException('decision.not_belongs_to_team');
        }

        if (!$team) {
            $team = $participant->getTeams()->first();
        }

        if (!$team->getMembers()->contains($user->getParticipation($team->getSeason()))) {
            throw new AccessDeniedHttpException('decision.not_belongs_to_team');
        }

        if (!$round) {
            /** @var DraftRound|DecisionRoundInterface|PeriodicRound $round */
            $round = $this->manager->getRepository(DraftRound::class)->findRoundForTeam(
                $season,
                $team
            );

            if (!$round) {
                /** @var Round[] $rounds */
                $rounds = $this->manager->getRepository(Round::class)->findActiveRounds($season);

                foreach ($rounds as $activeRound) {
                    if ($activeRound instanceof TimedRoundInterface && $activeRound->isFinished()) {
                        continue;
                    }

                    if ($activeRound instanceof FilteredRoundInterface && !$activeRound->hasTeam($team)) {
                        continue;
                    }

                    $round = $activeRound;
                    break;
                }
            }
        }

        if (!$round) {
            throw new AccessDeniedHttpException('decision.no_round_running');
        }

        if ($round instanceof FilteredRoundInterface && !$round->hasTeam($team)) {
            throw new AccessDeniedHttpException('decision.no_round_running');
        }

        if (!($round instanceof DecisionRoundInterface)) {
            throw new NotFoundHttpException('decision.non_interactive');
        }

        if (!$round->isDecisionAvailable()) {
            throw new AccessDeniedHttpException('decision.no_period_running');
        }

        $period = null;
        if ($round instanceof PeriodicRound) {
            $period = $round->getCurrentPeriod();
        }

        return [
            'round' => $round,
            'period' => $period,
            'team' => $team,
        ];
    }

    public function getActiveRoundOptionsHandled(Round $round = null, Team $team = null)
    {
        try {
            $options = $this->handleRoundOptions($round, $team);
        } catch (HttpException $exception) {
            return [
                'exception' => $exception,
                'round' => null,
                'team' => null,
                'period' => null,
            ];
        }

        return $options;
    }

    public function getTeamPlace(Round $round, Team $team)
    {
        if (!$round instanceof PeriodicRound) {
            return null;
        }

        $lastPeriod = null;
        foreach ($round->getPeriods() as $period) {
            if ($period->hasFinished() && $period->isRatingsPublished()) {
                $lastPeriod = $period;
            }
        }

        if (!$lastPeriod) {
            return null;
        }

        $teams = [];

        if (!($round instanceof FilteredRoundInterface)) {
            $teams = $this->manager->getRepository(Team::class)->findBy(['season' => $round->getSeason()]);
        } elseif ($round instanceof DraftRound) {
            $teamDraft = $round->getTeamDraft($team);
            foreach ($round->getDraft() as $record) {
                if ($record->league === $teamDraft->league && $record->group === $teamDraft->group) {
                    $teams[] = $record->getTeam();
                }
            }
        }

        $repository = $this->manager->getRepository(RatingRecord::class);

        /** @var RatingRecord[] $ratings */
        $ratings = $repository->findBy(['team' => $teams, 'period' => $lastPeriod], ['value' => 'DESC']);

        $place = 1;
        foreach ($ratings as $rating) {
            if ($rating->getTeam() === $team) {
                break;
            }
            $place++;
        }

        return ['place' => $place, 'total' => count($teams)];
    }
}
