<?php

declare(strict_types=1);

use Api\Handler\Venda\InserirHandler;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Zend\Expressive\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->post('/api/vendedor', Api\Handler\Vendedor\InserirHandler::class);
    $app->put('/api/vendedor/{id}', Api\Handler\Vendedor\AtualizarHandler::class);
    $app->delete('/api/vendedor/{id}', Api\Handler\Vendedor\ExcluirHandler::class);
    $app->get('/api/vendedor/{id}', Api\Handler\Vendedor\BuscarHandler::class);
    $app->get('/api/vendedor', Api\Handler\Vendedor\BuscarHandler::class);

    $app->post('/api/venda', Api\Handler\Venda\InserirHandler::class);
    $app->put('/api/venda/{id}', Api\Handler\Venda\AtualizarHandler::class);
    $app->delete('/api/venda/{id}', Api\Handler\Venda\ExcluirHandler::class);
    $app->get('/api/venda/{id}', Api\Handler\Venda\BuscarHandler::class);
    $app->get('/api/venda', Api\Handler\Venda\BuscarHandler::class);
    $app->get('/api/venda/vendedor/{id}', \Api\Handler\Venda\VendedorHandler::class);

    $app->post('/token', Auth\Handler\TokenHandler::class);

    $app->post('/email', \Api\Handler\Notificacao\EmailHandler::class);
    $app->get('/api/email', \Api\Handler\Notificacao\BuscarEmailHandler::class);
    $app->put('/api/email', \Api\Handler\Notificacao\AtualizarEmailHandler::class);


    $app->get('/api/dashboard/valorvendedor', \Api\Handler\Dashboard\ValorVendedor::class);
    $app->get('/api/dashboard/valordia', \Api\Handler\Dashboard\ValorDia::class);
    $app->get('/api/dashboard/comissaovendedor', \Api\Handler\Dashboard\ComissaoVendedor::class);
    $app->get('/api/dashboard/comissaodia', \Api\Handler\Dashboard\ComissaoDia::class);

};
