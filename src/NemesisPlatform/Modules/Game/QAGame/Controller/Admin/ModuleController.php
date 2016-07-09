<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.06.2015
 * Time: 15:00
 */

namespace NemesisPlatform\Modules\Game\QAGame\Controller\Admin;

use NemesisPlatform\Modules\Game\QAGame\Entity\QARound;
use NemesisPlatform\Modules\Game\QAGame\Entity\QuestionList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends Controller
{
    /**
     * @Route("/", name="module_qa_game_admin_dashboard")
     * @Template()
     * @return Response
     */
    public function dashboardAction()
    {
        $manager       = $this->getDoctrine()->getManager();
        $rounds        = $manager->getRepository(QARound::class)->findAll();
        $questionLists = $manager->getRepository(QuestionList::class)->findAll();

        return [
            'rounds'         => $rounds,
            'question_lists' => $questionLists,
        ];
    }
}
