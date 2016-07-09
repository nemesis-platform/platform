<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 20.06.2014
 * Time: 15:40
 */

namespace NemesisPlatform\Core\Account\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\TwigBundle\Controller as TwigController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * Class ExceptionController
 *
 * @package NemesisPlatform\Game\Controller\Site
 */
class ExceptionController extends TwigController\ExceptionController
{
    /**
     * Converts an Exception to a Response.
     *
     * @param Request              $request   The request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     * @param string               $_format   The format to use for rendering (html, xml, ...)
     *
     * @return Response
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     * @Template()
     */
    public function showAction(
        Request $request,
        FlattenException $exception,
        DebugLoggerInterface $logger = null,
        $_format = 'html'
    ) {
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

        $code = $exception->getStatusCode();

        $http_exception = in_array(
            'Symfony\Component\HttpKernel\Exception\HttpExceptionInterface',
            class_implements($exception->getClass())
        );

        return
            [
                'status_code'    => $code,
                'status_text'    => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception'      => $http_exception || $this->debug ? $exception : null,
                'logger'         => $logger,
                'currentContent' => $currentContent,
            ];
    }
}
