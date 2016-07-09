<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-11
 * Time: 16:11
 */

namespace NemesisPlatform\Core\Account\Controller;

use NemesisPlatform\Core\Account\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountController
 *
 * @Route("/account")
 */
class AccountController extends Controller
{

    /**
     * @return Response
     * @Template()
     * @Route("/", name="site_account_show")
     */
    public function showAction()
    {
        /** @var User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return ['user' => $user,];
    }
}
