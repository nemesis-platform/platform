<?php

namespace NemesisPlatform\Admin\Command;

use NemesisPlatform\Game\Entity\SeasonedSite;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSiteCommand extends ContainerAwareCommand
{
    /** {@inheritDoc} */
    protected function configure()
    {
        $this
            ->setName('nemesis:admin:create-site')
            ->setDescription('Create admin user')
            ->addArgument('domain', InputArgument::REQUIRED, 'Site domain')
            ->addArgument('short-name', InputArgument::REQUIRED, 'Site short name')
            ->setHelp('Creates activated site');
    }

    /** {@inheritDoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain = $input->getArgument('domain');
        $site   = new SeasonedSite($domain, $input->getArgument('short-name'));
        $site->setActive(true);
        $site->setSupportEmail('support@'.$domain);

        $this->getContainer()->get('doctrine.orm.entity_manager')->persist($site);
        $this->getContainer()->get('doctrine.orm.entity_manager')->flush();
    }

}
