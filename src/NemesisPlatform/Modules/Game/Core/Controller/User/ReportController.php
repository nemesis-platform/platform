<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 18:04
 */

namespace NemesisPlatform\Modules\Game\Core\Controller\User;

use NemesisPlatform\Modules\Game\Core\Entity\Report;
use NemesisPlatform\Core\Account\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ReportController
 *
 * @package NemesisPlatform\Modules\Game\Core\Controller\User
 * @Route("/reports")
 */
class ReportController extends Controller
{

    /**
     * @Route("/", name="game_core_reports_list")
     * @Method("GET")
     * @Template()
     * @return Response
     */
    public function listAction()
    {
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $teams = [];
        foreach ($user->getParticipations() as $sData) {
            foreach ($sData->getTeams() as $team) {
                $teams[] = $team;
            }
        }

        $reports = $this->getDoctrine()->getRepository(Report::class)->findBy(
            ['team' => $teams]
        );

        return ['reports' => $reports];
    }

    /**
     * @param Report $report
     *
     * @return BinaryFileResponse
     * @Route("/{report}/download", name="game_core_report_download")
     * @Method("GET")
     *
     */
    public function viewAction(Report $report)
    {
        /** @var User $user */
        $user = $this->getUser();

        $teams = [];
        foreach ($user->getParticipations() as $sData) {
            foreach ($sData->getTeams() as $team) {
                $teams[] = $team;
            }
        }

        if (!in_array($report->getTeam(), $teams)) {
            throw new AccessDeniedHttpException('Нет доступа к этому отчету');
        }

        if (!$report->getPeriod()->isReportsPublished()) {
            throw new AccessDeniedHttpException('Нет доступа к этому отчету');
        }

        $response = new BinaryFileResponse(
            $this->get('service_container')->getParameter('reports_storage').$report->getStorageId(),
            Response::HTTP_OK,
            [],
            false
        );
        $response->setContentDisposition('attachment', $report->getFilename());

        return $response;
    }
}
