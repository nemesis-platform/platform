<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-05-26
 * Time: 22:47
 */

namespace NemesisPlatform\Admin\Exporter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use NemesisPlatform\Components\ExportImport\Service\ExporterInterface;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractTeamExporter extends AbstractCSVExporter implements ExporterInterface, FormInjectorInterface
{
    /** @var  EntityManagerInterface */
    protected $manager;
    /** @var null|\NemesisPlatform\Game\Entity\Season[] */
    protected $seasons = null;

    /**
     * AbstractTeamExporter constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function injectForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add(
                'encoding',
                'choice',
                [
                    'label'   => 'Кодировка',
                    'choices' => [
                        'windows-1251' => 'Для русского экселя',
                        'utf-8'        => 'Для нормальных программ',
                    ],
                ]
            )
            ->add(
                'season',
                'site_seasons',
                [
                    'label'    => 'Сезоны',
                    'attr'     => [
                        'help_text' => 'Выберите сезоны или будут скачаны все активные сезоны',
                    ],
                    'multiple' => true,
                ]
            );
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function export(array $options = [])
    {
        /** @var PersistentCollection $options ["season"] */
        /** @var \NemesisPlatform\Game\Entity\Season[] $seasons */
        $seasons = null;

        $now = new \DateTime();
        if (count($options['season']) > 0) {
            $this->seasons = $options['season']->toArray();
        } else {
            $this->seasons = $this->manager->getRepository(Season::class)->findBy(['active' => true]);
        }
        $seasons = $this->seasons;

        if (count($seasons) > 0) {
            $filename = tempnam(sys_get_temp_dir(), 'exporter_');
            $response = new BinaryFileResponse($filename);

            if (count($seasons) > 1) {
                $zip = new \ZipArchive();

                $zip->open($filename);

                foreach ($seasons as $season) {
                    $sfn
                        = 'teams_'.preg_replace('/[^\w\d]/', '_', $season->getShortName()).'_'.$now->format('Ymd')
                        .'.csv';
                    $zip->addFromString(
                        preg_replace('/[^\w\d]/', '_', $season->getSite()->getShortName()).'/'.$sfn,
                        $this->getContentForSeason($season)
                    );
                }
                $zip->close();

                $response->setContentDisposition('attachment', $this->getType().'_'.$now->format('Ymd').'.zip');
            } else {
                $content         = $this->getContentForSeason($seasons[0]);
                $siteShortName   = preg_replace('/[^\a-zA-Z\d]/', '_', $seasons[0]->getSite()->getShortName());
                $seasonShortName = preg_replace('/[^\a-zA-Z\d]/', '_', $seasons[0]->getShortName());
                $attachment
                                 = $siteShortName.'_teams_'.$seasonShortName.'_'.$now->format('Ymd').'.csv';

                $response->setContentDisposition(
                    'attachment',
                    $attachment
                );
                file_put_contents($filename, $content);
            }
            $response->deleteFileAfterSend(true);

            return $response;
        }

        return new Response();
    }

    protected function getContentForSeason(Season $season)
    {
        $content = implode($this->delimiter, $this->escapeChunks($this->getHeaders())).PHP_EOL;

        $teams = $this->manager->getRepository(Team::class)->prefetchSeason($season);

        foreach ($teams as $team) {
            $content .= implode($this->delimiter, $this->escapeChunks($this->teamToArray($team))).PHP_EOL;
        }

        return $content;
    }

    abstract protected function getHeaders();

    abstract protected function teamToArray(Team $team);
}
