<?php

namespace NemesisPlatform\Components\Skins\Command;

use NemesisPlatform\Components\Skins\Entity\SkinConfigurationInterface;
use NemesisPlatform\Components\Skins\Service\CompilableThemeInterface;
use NemesisPlatform\Components\Skins\Service\ConfigurableThemeInterface;
use NemesisPlatform\Components\Skins\Service\LayoutStorageInterface;
use NemesisPlatform\Components\Skins\Service\SkinRegistry;
use NemesisPlatform\Core\CMS\Entity\ThemeInstance;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    /** {@inheritdoc} */
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
        $this->executeGenerateBootstrap($output);
    }

    protected function executeGenerateBootstrap(OutputInterface $output)
    {
        /** @var SkinRegistry $skin_registry */
        $skin_registry = $this->getContainer()->get('nemesis.skin_registry');
        /** @var LayoutStorageInterface[]|CompilableThemeInterface[]|ConfigurableThemeInterface[] $themes */
        $themes  = $skin_registry->all();
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        foreach ($themes as $type => $theme) {

            $output->writeln(
                sprintf('<info>Generating theme <comment>%s</comment></info>', $type)
            );

            if ($theme instanceof CompilableThemeInterface) {
                if ($theme instanceof ConfigurableThemeInterface) {

                    /** @var LayoutStorageInterface|CompilableThemeInterface|ConfigurableThemeInterface $theme */
                    /** @var ThemeInstance[] $configurations */
                    $configurations = $manager->getRepository(SkinConfigurationInterface::class)->findBy(
                        ['theme' => $type]
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
                                $theme->configure($instance->getConfig());
                                $theme->compile();
                                $output->writeln(' [<info>DONE</info>]');
                            } catch (\Exception $exception) {
                                $output->write(' [<stacktrace>FAIL</stacktrace>] ');
                                $output->writeln(sprintf('<stacktrace>%s</stacktrace>', $exception->getMessage()));
                                if ($output->getVerbosity() > OutputInterface::VERBOSITY_VERBOSE) {
                                    $output->writeln(
                                        sprintf('<stacktrace>%s</stacktrace>', $exception->getTraceAsString())
                                    );
                                }
                                continue;
                            }
                        }
                    }
                }

                try {
                    $output->write(' - <info>Configuration <comment>Zero-Config</comment></info> ');
                    $theme->compile();
                    $output->writeln(' [<info>DONE</info>]');
                } catch (\Exception $exception) {
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
