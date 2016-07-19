<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 20.06.2014
 * Time: 15:40
 */

namespace NemesisPlatform\Core\Account\Controller;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * Class ExceptionController
 *
 * @package NemesisPlatform\Game\Controller\Site
 */
class ExceptionController
{
    /**
     * @var bool Show error (false) or exception (true) pages by default
     */
    private $debug;
    /** @var \Twig_Environment */
    private $twig;

    public function __construct(\Twig_Environment $twig, $debug)
    {
        $this->twig  = $twig;
        $this->debug = $debug;
    }

    /**
     * Converts an Exception to a Response.
     *
     * @param Request              $request   The request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     *
     * @return Response|array
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     * @throws \InvalidArgumentException
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

        $code = $exception->getStatusCode();

        $http_exception = $exception instanceof HttpExceptionInterface;

        return new Response(
            $this->twig->render(
                (string)$this->findTemplate($request, $request->getRequestFormat(), $code, $this->debug),
                [
                    'status_code'    => $code,
                    'status_text'    => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception'      => $http_exception || $this->debug ? $exception : null,
                    'logger'         => $logger,
                    'currentContent' => $currentContent,
                ]
            )
        );
    }


    /**
     * @param int $startObLevel
     *
     * @return string
     */
    protected function getAndCleanOutputBuffering($startObLevel)
    {
        if (ob_get_level() <= $startObLevel) {
            return '';
        }

        Response::closeOutputBuffers($startObLevel + 1, true);

        return ob_get_clean();
    }

    /**
     * @param Request $request
     * @param string  $format
     * @param int     $code An HTTP response status code
     * @param bool    $showException
     *
     * @return string
     */
    private function findTemplate(Request $request, $format, $code, $showException)
    {
        $name = $showException ? 'exception' : 'error';
        if ($showException && 'html' === $format) {
            $name = 'exception_full';
        }

        // For error pages, try to find a template for the specific HTTP status code and format
        if (!$showException) {
            $template = sprintf('@NemesisCor/Exception/%s%s.%s.twig', $name, $code, $format);
            if ($this->templateExists($template)) {
                return $template;
            }
        }

        // try to find a template for the given format
        $template = sprintf('@NemesisCore/Exception/%s.%s.twig', $name, $format);
        if ($this->templateExists($template)) {
            return $template;
        }

        // default to a generic HTML exception
        $request->setRequestFormat('html');

        return sprintf('@NemesisCore/Exception/%s.html.twig', $showException ? 'exception_full' : $name);
    }

    // to be removed when the minimum required version of Twig is >= 3.0
    private function templateExists($template)
    {
        $template = (string)$template;

        $loader = $this->twig->getLoader();
        try {
            $loader->getSource($template);

            return true;
        } catch (\Twig_Error_Loader $e) {
        }

        return false;
    }
}
