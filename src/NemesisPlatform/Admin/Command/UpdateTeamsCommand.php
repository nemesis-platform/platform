<?php

/**
 * This file is part of BraincraftedBootstrapBundle.
 *
 * (c) 2012-2013 by Florian Eckerstorfer
 */

namespace NemesisPlatform\Admin\Command;

use Exception;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Game\Repository\TeamListener;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateTeamsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function configure()
    {
        $this
            ->setName('nemesis:teams:touch')
            ->setDescription('Touch teams')
            ->setHelp('Force update team entities')
            ->addArgument(
                'season',
                InputArgument::OPTIONAL,
                'Season to update. If not supplied - all active seasons will be updated'
            )//            ->addOption('debug', 'd', InputArgument::OPTIONAL, 'Debug mode', false)
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');

        if ($input->getArgument('season')) {
            $season = $manager->getRepository(Season::class)->find($input->getArgument('season'));

            if (!$season) {
                throw new Exception('Season object not found');
            }

            $seasons = [$season];
        } else {
            $seasons = $manager->getRepository(Season::class)->findBy(['active' => true]);
        }

        $teams = $manager->getRepository(Team::class)->findBy(['season' => $seasons]);

        $count = 0;

        foreach ($teams as $team) {
            TeamListener::updateTeam($team);

            $count++;
        }

        $manager->flush();
        $output->writeln(sprintf('Updated %d teams', $count));
    }
}
