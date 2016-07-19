<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 18:04
 */

namespace NemesisPlatform\Game\Controller;

use NemesisPlatform\Game\Entity\Certificate\ParticipantCertificate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CertificateController extends Controller
{

    /**
     * @return string
     * @Route("/game/certificates", name="game_certificates_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        /** @var ParticipantCertificate[] $participantCerts */
        $manager          = $this->getDoctrine()->getManager();
        $participantCerts = $manager->getRepository(ParticipantCertificate::class)
            ->findBy(
                ['owner' => $this->get('security.token_storage')->getToken()->getUser()]
            );

        $pcBySeason = [];

        foreach ($participantCerts as $cert) {
            $pcBySeason[$cert->getSeason()->getId()][] = $cert;
        }

        return ['participant_certs' => $pcBySeason];
    }
}
