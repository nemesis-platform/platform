<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.10.2014
 * Time: 13:31
 */

namespace NemesisPlatform\Admin\Exporter;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use NemesisPlatform\Components\ExportImport\Service\ExporterInterface;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Season;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class ParticipantExporter implements ExporterInterface, FormInjectorInterface
{
    /** @var  EntityManagerInterface */
    private $manager;
    /** @var string */
    private $encoding = 'windows-1251';
    /** @var string */
    private $delimiter = ';';
    /** @var string */
    private $enclosure = '"';

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function export(array $options = [])
    {
        /** @var PersistentCollection $options ["season"] */
        /** @var Season[] $seasons */
        $seasons = null;

        $now = new DateTime();
        if (count($options['season']) > 0) {
            $seasons = $options['season']->toArray();
        } else {
            $seasons = $this->manager->getRepository(Season::class)->findBy(['active' => true]);
        }

        if (count($seasons) > 0) {
            $filename = tempnam(sys_get_temp_dir(), 'exporter_');
            $response = new BinaryFileResponse($filename);

            if (count($seasons) > 1) {
                $zip = new ZipArchive();

                $zip->open($filename);

                foreach ($seasons as $season) {
                    $siteName   = mb_ereg_replace('[^a-zA-Z0-9]', '_', $season->getSite()->getShortName());
                    $seasonName = mb_eregi_replace('[^a-zA-Z0-9]', '_', $season->getShortName());

                    $sfn = 'participants_'.$seasonName.'_'.$now->format('Ymd').'.csv';

                    $zip->addFromString($siteName.'/'.$sfn, $this->getContentForSeason($season));
                }
                $zip->close();

                $response->setContentDisposition('attachment', $this->getType().'_'.$now->format('Ymd').'.zip');
            } else {
                $content = $this->getContentForSeason($seasons[0]);

                $siteName   = mb_ereg_replace('[^a-zA-Z0-9]', '_', $seasons[0]->getSite()->getShortName());
                $seasonName = mb_eregi_replace('[^a-zA-Z0-9]', '_', $seasons[0]->getShortName());

                $attachment = $siteName.'_participants_'.$seasonName.'_'.$now->format('Ymd').'.csv';

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

    private function getContentForSeason(Season $season)
    {
        $content = implode($this->delimiter, $this->escapeChunks($this->getHeaders())).PHP_EOL;

        $participants = $this->manager->getRepository(Participant::class)->prefetchSeason($season);

        foreach ($participants as $participant) {
            $content .= implode($this->delimiter, $this->escapeChunks($this->participantToArray($participant)));
            $content .= PHP_EOL;
        }

        return $content;
    }

    /**
     * @param string[] $chunks
     *
     * @return string[]
     */
    private function escapeChunks($chunks)
    {
        $escaped = [];

        foreach ($chunks as $chunk) {
            $escaped[] = $this->escape($chunk);
        }

        return $escaped;
    }

    /**
     * @param string $chunk
     *
     * @return string
     */
    private function escape($chunk)
    {
        $delimiterEsc = preg_quote($this->delimiter, '/');
        $enclosureEsc = preg_quote($this->enclosure, '/');

        $chunk = mb_convert_encoding($chunk, $this->encoding, 'utf-8');

        return preg_match("/(?:${delimiterEsc}|${enclosureEsc}|\s)/", $chunk) ? (
            $this->enclosure.str_replace(
                $this->enclosure,
                $this->enclosure.$this->enclosure,
                $chunk
            ).$this->enclosure
        ) : $chunk;
    }

    private function getHeaders()
    {
        $headers = [
            'id',
            'email',
            'fio',
            'phone',
            'user_id',
            'birthdate',
            'site',
            'season',
            'category',
            'league',
            'created',
            'first_team',
        ];


        $fields = $this->manager->getRepository(AbstractField::class)->findAll();

        foreach ($fields as $field) {
            $headers[] = sprintf(
                '%s (%s)',
                $field->getTitle(),
                $field->getName()
            );
        }

        return $headers;
    }

    /**
     * @param Participant $participant
     *
     * @return string[]
     */
    private function participantToArray(Participant $participant)
    {
        $result = [];

        $result['id']         = $participant->getId();
        $result['email']      = $participant->getUser()->getEmail();
        $result['fio']        = $participant->getUser()->getFormattedName();
        $result['phone']      = $participant->getUser()->getPhone() ? $participant->getUser()->getPhone()
            ->getPhonenumber() : '';
        $result['user_id']    = $participant->getUser()->getID();
        $result['birthdate']  = $participant->getUser()->getBirthdate() ?
            $participant->getUser()->getBirthdate()->format('Y.m.d H:i:s') : '';
        $result['site']       = $participant->getSeason()->getSite()->getFullName();
        $result['season']     = $participant->getSeason()->getName();
        $result['category']   = $participant->getCategory() ? $participant->getCategory()->getName() : '';
        $result['league']     = $participant->getCategory() ? ($participant->getCategory()->getLeague()
            ? $participant->getCategory()->getLeague()->getName() : '') : '';
        $result['created']    = $participant->getCreated()->format('Y.m.d H:i:s');
        $result['first_team'] = (count($participant->getTeams()) > 0) ? $participant->getTeams()[0]->getID() : '';

        $fields = $this->manager->getRepository(AbstractField::class)->findAll();

        foreach ($fields as $field) {
            $result[sprintf('%s (%s)', $field->getTitle(), $field->getName())] = '';

            foreach ($participant->getValues() as $value) {
                if ($value->getField() === $field) {
                    $result[sprintf('%s (%s)', $field->getTitle(), $field->getName())] = $value->getRenderValue();
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'participant_simple_exporter';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Экспорт участников';
    }

    public function injectForm(FormBuilderInterface $builder)
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
}
