<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.06.2015
 * Time: 15:20
 */

namespace NemesisPlatform\Modules\Game\QAGame\Exporter;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\ExportImport\Service\ExporterInterface;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Modules\Game\Core\Entity\Decision;
use NemesisPlatform\Modules\Game\QAGame\Entity\QADecision;
use NemesisPlatform\Modules\Game\QAGame\Entity\QARound;
use NemesisPlatform\Modules\Game\QAGame\Repository\QADecisionRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use TCPDF;

class QADecisionPDFExporter implements ExporterInterface, FormInjectorInterface
{
    private $twig;
    private $manager;

    public function __construct(EntityManagerInterface $manager, \Twig_Environment $twig)
    {
        $this->manager = $manager;
        $this->twig    = $twig;
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function export(array $options = [])
    {
        $now = new \DateTime();

        $filename = tempnam(sys_get_temp_dir(), 'exporter_');
        $zip      = new \ZipArchive();

        $zip->open($filename);

        $propetyPaths = $options['paths'];

        /** @var QADecisionRepository $repository */
        $repository = $this->manager->getRepository(QADecision::class);

        $decisions = $repository->getRoundDecisions($options['round']);

        foreach ($decisions as $decision) {
            if (!$this->isSupported($decision)) {
                continue;
            }

            $data = [
                'team'      => $decision->getTeam(),
                'decision'  => $decision,
                'answers'   => $decision->getAnswers(),
                'captain'   => $decision->getTeam()->getCaptain(),
                'user'      => $decision->getTeam()->getCaptain()->getUser(),
                'submitter' => $decision->getAuthor(),
                'season'    => $decision->getTeam()->getSeason(),
            ];

            $resolvedPaths = [];

            $accessor = new PropertyAccessor();

            foreach ($propetyPaths as $subPath) {
                try {
                    $resolvedPaths[] = $accessor->getValue($data, $subPath);
                } catch (\Exception $e) {
                    throw new \InvalidArgumentException(
                        sprintf('Путь "%s" не найден в (%s)', $subPath, implode(', ', array_keys($data)))
                    );
                }
            }

            $path = (count($resolvedPaths) !== 0) ?
                implode(
                    DIRECTORY_SEPARATOR,
                    array_map(
                        function ($subPath) {
                            return str_replace('?', '', mb_convert_encoding($subPath, 'CP866', 'UTF-8'));
                        },
                        $resolvedPaths
                    )
                ).DIRECTORY_SEPARATOR : '';

            $decision_filename = str_replace(
                '?',
                '',
                mb_convert_encoding($accessor->getValue($data, $options['filename']).'.pdf', 'CP866', 'UTF-8')
            );

            $content = $this->getDecisionContent($decision);

            $zip->addFromString($path.$decision_filename, $content);
        }

        $zip->close();

        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition('attachment', $this->getType().'_'.$now->format('Ymd').'.zip');
        $response->deleteFileAfterSend(true);

        return $response;
    }

    protected function isSupported($entity)
    {
        return $entity instanceof QADecision;
    }

    protected function getDecisionContent(Decision $decision)
    {
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT);

        $team     = $decision->getTeam();
        $mCaptain = $team->getCaptain();
        $uCaptain = $mCaptain->getUser();

        // set document information
        $pdf->SetCreator($uCaptain->getFormattedName());
        $pdf->setFooterData([0, 64, 0], [0, 64, 128]);

        $pdf->setDocModificationTimestamp($decision->getSubmissionTime()->getTimestamp());
        $pdf->setDocCreationTimestamp($decision->getSubmissionTime()->getTimestamp());

        // set header and footer fonts
        $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 14);
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        $html = $this->twig->render($this->getTemplate(), ['decision' => $decision]);

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        $content = $pdf->Output('', 'S');

        return $content;
    }

    /**
     * @return string Template
     */
    protected function getTemplate()
    {
        return 'QAGameBundle:Exporter:decision_pdf.html.twig';
    }

    /**
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'qa_decision_pdf_exporter';
    }

    public function injectForm(FormBuilderInterface $builder)
    {
        $builder->add(
            'round',
            'current_site_rounds',
            [
                'class' => QARound::class,
            ]
        );
        $builder->add(
            'paths',
            'collection',
            [
                'type'         => 'text',
                'allow_add'    => true,
                'allow_delete' => true,
                'label'        => 'Компоненты пути',
            ]
        );
        $builder->add(
            'filename',
            'text',
            [
                'data'  => '[team].name',
                'label' => 'Имя файла',
            ]
        );
    }
}
