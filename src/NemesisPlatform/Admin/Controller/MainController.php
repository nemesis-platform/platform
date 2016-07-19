<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.06.2014
 * Time: 11:43
 */

namespace NemesisPlatform\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MainController
 *
 * @package NemesisPlatform\Admin\Controller
 */
class MainController extends Controller
{
    /**
     * @return string
     * @Route("/",name="site_admin_dashboard")
     * @Method("GET")
     * @Template()
     */
    public function dashboardAction()
    {
        return [];
    }
}
