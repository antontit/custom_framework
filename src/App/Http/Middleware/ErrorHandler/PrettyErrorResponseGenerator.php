<?php


namespace App\Http\Middleware\ErrorHandler;

use Framework\Template\TemplateRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Stratigility\Utils;

class PrettyErrorResponseGenerator implements ErrorResponseGenerator
{
    private $template;
    private $views;
    private $response;

    public function __construct(
        TemplateRenderer $templateRenderer,
        ResponseInterface $response,
        array $views
    ) {
        $this->template = $templateRenderer;
        $this->views = $views;
        $this->response = $response;
    }

    public function generate(ServerRequestInterface $request, $e): ResponseInterface
    {
        $code = Utils::getStatusCode($e, $this->response);

        $response = $this->response->withStatus($code);
        $response->getBody()->write($this->template->render($this->getView($code), [
            'request' => $request,
            'exception' => $e,
        ]));

        return $response;
    }

    private function getView($code): string
    {
        if (array_key_exists($code, $this->views)) {
            $view = $this->views[$code];
        } else {
            $view = $this->views['error'];
        }
        return $view;
    }
}
