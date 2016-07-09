<?php

/**
 * Based on BraincraftedBootstrapBundle generator command ((c) 2012-2013 by Florian Eckerstorfer)
 */

namespace NemesisPlatform\Components\Themes\Command;

use Exception;
use NemesisPlatform\Components\Themes\Entity\ThemeInstance;
use NemesisPlatform\Components\Themes\Service\CompilableThemeInterface;
use NemesisPlatform\Components\Themes\Service\ConfigurableThemeInterface;
use NemesisPlatform\Components\Themes\Service\ThemeInterface;
use NemesisPlatform\Components\Themes\Service\ThemeRegistry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function configure()
    {
        $this
            ->setName('nemesis:themes:generate')
            ->setDescription('Compile custom themes to end assets');

    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setDecorated(true);
        $style = new OutputFormatterStyle('red');
        $output->getFormatter()->setStyle('stacktrace', $style);
        $this->executeGenerateBootstrap($input, $output);
    }

    protected function executeGenerateBootstrap(InputInterface $input, OutputInterface $output)
    {
        /** @var ThemeRegistry $theme_registry */
        $theme_registry = $this->getContainer()->get('scaytrase.theme_registry');
        /** @var ThemeInterface[]|CompilableThemeInterface[]|ConfigurableThemeInterface[] $themes */
        $themes = $theme_registry->all();
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        foreach ($themes as $theme) {

            $output->writeln(
                sprintf('<info>Generating theme <comment>%s</comment></info>', $theme->getType())
            );

            if ($theme instanceof CompilableThemeInterface) {
                if ($theme instanceof ConfigurableThemeInterface) {

                    /** @var ThemeInterface|CompilableThemeInterface|ConfigurableThemeInterface $theme */
                    /** @var ThemeInstance[] $configurations */
                    $configurations = $manager->getRepository('SwitchableThemeBundle:ThemeInstance')->findBy(
                        array('theme' => $theme->getType())
                    );

                    if (count($configurations) === 0) {
                        $output->writeln(' <comment>No stored configurations found</comment>');
                    } else {
                        foreach ($configurations as $instance) {
                            $output->write(
                                sprintf(
                                    ' - <info>Configuration <comment>%s</comment></info> ',
                                    $instance->getDescription()
                                )
                            );
                            try {
                                $theme->setConfiguration($instance->getConfig());
                                $theme->compile();
                                $output->writeln(' [<info>DONE</info>]');
                            } catch (Exception $exception) {
                                $output->write(' [<stacktrace>FAIL</stacktrace>] ');
                                $output->writeln(sprintf('<stacktrace>%s</stacktrace>', $exception->getMessage()));
                                if ($output->getVerbosity() > OutputInterface::VERBOSITY_VERBOSE) {
                                    $output->writeln(sprintf('<stacktrace>%s</stacktrace>', $exception->getTraceAsString()));
                                }
                                continue;
                            }
                        }
                    }

                }

                try {
                    $output->write(' [<info>Generating zeroconf configuration</info>] ');
                    $theme->compile();
                    $output->writeln(' [<info>DONE</info>]');
                } catch (Exception $exception) {
                    $output->write(' [<stacktrace>FAIL</stacktrace>] ');
                    $output->writeln(sprintf('<stacktrace>%s</stacktrace>', $exception->getMessage()));
                    if ($output->getVerbosity() > OutputInterface::VERBOSITY_VERBOSE) {
                        $output->writeln(sprintf('<stacktrace>%s</stacktrace>', $exception->getTraceAsString()));
                    }
                    continue;
                }
            }

        }
    }
}
