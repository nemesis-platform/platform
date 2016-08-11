<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 01.06.2015
 * Time: 16:11
 */

namespace NemesisPlatform\Admin\Exporter;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Components\ExportImport\Service\ExporterInterface;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Form\Survey\Entity\Survey;
use NemesisPlatform\Components\MultiSite\Service\SiteProviderInterface;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Core\CMS\Entity\SiteSurvey;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class SurveyExporter extends AbstractCSVExporter implements ExporterInterface, FormInjectorInterface
{
    /** @var  SiteProviderInterface */
    private $siteManager;

    /**
     * SurveyExporter constructor.
     *
     * @param SiteProviderInterface $siteManager
     */
    public function __construct(SiteProviderInterface $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function export(array $options = [])
    {
        if (!array_key_exists('survey', $options)) {
            throw new \LogicException('Survey not provided');
        }
        /** @var Survey $survey */
        $survey = $options['survey'];

        $results = $survey->getResults();

        $questions = $survey->getQuestions();

        $headers = [];

        $headers[] = 'id';
        $headers[] = 'survey_id';
        $headers[] = 'timeCreated';
        $headers[] = 'timeUpdated';
        $headers[] = 'author_id';

        foreach ($questions as $question) {
            $headers[] = $question->getField()->getName();
        }

        $data = [$headers];

        foreach ($results as $result) {
            $row = [];

            $row[]  = $result->getId();
            $row[]  = $survey->getId();
            $row[]  = $result->getTimeCreated()->format('Y.m.d H:i:s');
            $row[]  = $result->getTimeUpdated()->format('Y.m.d H:i:s');
            $author = $result->getAuthor();
            if ($author instanceof User) {
                $row[] = $author->getID();
            } else {
                $row[] = '';
            }
            foreach ($questions as $question) {
                $currentAnswer = null;
                foreach ($result->getAnswers() as $answer) {
                    if ($answer->getValue()->getField()->getName() === $question->getField()->getName()) {
                        $currentAnswer = $answer;
                        break;
                    }
                }
                if ($currentAnswer !== null) {
                    $value = $currentAnswer->getValue()->getRenderValue();

                    if (is_array($value)) {
                        $value = $this->arrayToString($value);
                    }

                    $row[] = (string)$value;
                } else {
                    $row[] = '';
                }
            }

            $data[] = $row;
        }

        $content = '';

        foreach ($data as $row) {
            $content .= implode($this->delimiter, $this->escapeChunks($row)).PHP_EOL;
        }

        $filename = tempnam(sys_get_temp_dir(), 'exporter_');
        $response = new BinaryFileResponse($filename);
        file_put_contents($filename, $content);
        $response->setContentDisposition(
            'attachment',
            $survey->getAlias().'.csv'
        );
        $response->deleteFileAfterSend(true);

        return $response;
    }

    private function arrayToString(array $array)
    {
        $content = '';
        foreach ($array as $row) {
            if (is_array($row)) {
                $row = implode(', ', $row);
            }
            $content .= $row.PHP_EOL;
        }

        return $content;
    }

    /**
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'site_survey_exporter';
    }

    public function injectForm(FormBuilderInterface $builder)
    {
        $builder->add(
            'survey',
            'entity',
            [
                'class'         => SiteSurvey::class,
                'required'      => true,
                'empty_value'   => 'Select survey to export',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository
                        ->createQueryBuilder('survey')
                        ->where('survey.site = :site')
                        ->setParameter('site', $this->siteManager->getSite());
                },
            ]
        );
    }
}
