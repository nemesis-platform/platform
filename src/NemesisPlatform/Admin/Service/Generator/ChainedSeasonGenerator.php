<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.02.2015
 * Time: 13:16
 */

namespace NemesisPlatform\Admin\Service\Generator;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\MultiSite\Service\SiteProviderInterface;
use NemesisPlatform\Game\Entity\Season;
use Symfony\Component\Form\FormBuilderInterface;

class ChainedSeasonGenerator implements EntityGeneratorInterface, FormInjectorInterface
{
    /** @var EntityManagerInterface */
    private $manager;
    /** @var UserGenerator */
    private $userGenerator;
    /** @var ParticipantGenerator */
    private $participantGenerator;
    /** @var TeamGenerator */
    private $teamGenerator;
    /** @var SiteProviderInterface */
    private $siteManager;

    /**
     * @param EntityManagerInterface $manager
     * @param SiteProviderInterface  $siteManager
     * @param UserGenerator          $userGenerator
     * @param ParticipantGenerator   $participantGenerator
     * @param TeamGenerator          $teamGenerator
     */
    public function __construct(
        EntityManagerInterface $manager,
        SiteProviderInterface $siteManager,
        UserGenerator $userGenerator,
        ParticipantGenerator $participantGenerator,
        TeamGenerator $teamGenerator
    ) {
        $this->manager              = $manager;
        $this->siteManager          = $siteManager;
        $this->userGenerator        = $userGenerator;
        $this->participantGenerator = $participantGenerator;
        $this->teamGenerator        = $teamGenerator;
    }

    /**
     * @param array $data
     *
     * @return GenerationReportInterface
     */
    public function generate($data = [])
    {
        $site       = $this->siteManager->getSite();
        $seasonName = $data['season_name'];
        $teamCount  = $data['teams'];

        $uRows = [$this->userGenerator->getType().'_rows' => []];
        for ($i = 0; $i < $teamCount; $i++) {
            $uRows[$this->userGenerator->getType().'_rows'][] = null;
        }

        $season = $data['season'];

        if (!$season && !$seasonName) {
            throw new \InvalidArgumentException('Provide either season or name for new season');
        }

        if (!$season) {
            $season = new Season();
            $season->setSite($site);
            $season->setName($seasonName);
            $season->setShortName($seasonName);
            $season->setActive(true);
            $season->setDescription('Демо сезон');
            $season->setRegistrationOpen(false);
        }

        $this->manager->persist($season);
        $this->manager->flush();

        $rData = [];

        $report = new GenerationReport();

        // Generate Users
        $userReport = $this->userGenerator->generate($uRows);
        $sRows      = array_map(
            function ($row) {
                return $row['user']->getId();
            },
            $userReport->getData()
        );


        foreach ($userReport->getNotifications() as $notification) {
            $report->addNotification($notification);
        }
        foreach ($userReport->getData() as $email => $row) {
            $rData[$row['user']->getId()]['user_id']  = $row['user']->getId();
            $rData[$row['user']->getId()]['email']    = $row['user']->getEmail();
            $rData[$row['user']->getId()]['password'] = $row['password'];
        }

        // Generate Participants
        $partReport = $this->participantGenerator->generate(
            [$this->participantGenerator->getType().'_rows' => $sRows, 'season' => $season]
        );
        $tRows      = array_map(
            function ($row) {
                return $row['sData']->getId();
            },
            $partReport->getData()
        );

        foreach ($partReport->getData() as $email => $row) {
            $rData[$row['user']->getId()]['sdata_id'] = $row['sData']->getId();
        }
        foreach ($partReport->getNotifications() as $notification) {
            $report->addNotification($notification);
        }

        // Generate Teams
        $teamReport = $this->teamGenerator->generate([$this->teamGenerator->getType().'_rows' => $tRows]);
        foreach ($teamReport->getData() as $email => $row) {
            $rData[$row['captain']->getUser()->getId()]['team_id']   = $row['team']->getId();
            $rData[$row['captain']->getUser()->getId()]['team_name'] = $row['team']->getName();
        }
        foreach ($teamReport->getNotifications() as $notification) {
            $report->addNotification($notification);
        }

        foreach ($rData as $row) {
            $report->addDataRow($row);
        }

        return $report;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Цепной генератор сезона';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'chained_season_generator';
    }

    public function injectForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('season', 'site_seasons', ['label' => 'Сезон', 'required' => false])
            ->add('season_name', 'text', ['required' => false])
            ->add('teams', 'integer');
    }
}
