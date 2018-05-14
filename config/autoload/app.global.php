<?php

use Framework\Http\Router\AuraRouterAdapter;
use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Application;
use Framework\Http\Router\Router;
use Zend\Diactoros\Response;
use App\Middleware\ErrorHandlerMiddleware;

return [
    'dependencies' => [
        'abstract_factories' => [
            Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory::class
        ],
        'factories' => [
            Application::class => function ($container){
                $app = new Application(
                    $container->get(MiddlewareResolver::class),
                    $container->get(Router::class),
                    new App\Middleware\NotFoundHandler(),
                    new Response()
                );
                $app->basePath($container->get('config')['basePath'] ?? null);

                return $app;
            },

            MiddlewareResolver::class => function ($container){
                return new MiddlewareResolver($container);
            },

            ErrorHandlerMiddleware::class => function ($container){
                return new App\Middleware\ErrorHandlerMiddleware($container->get('config')['debug']);
            },

            Router::class => function (){
                return new AuraRouterAdapter(new Aura\Router\RouterContainer());
            },
        ]
    ],
    'debug' => false,
    'basePath' => null,
];



