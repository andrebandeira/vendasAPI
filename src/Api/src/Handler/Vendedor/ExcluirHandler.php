<?php

declare(strict_types=1);

namespace Api\Handler\Vendedor;

use Core\BD\BD;
use Core\Handler\MainHandler;
use Core\Json\JsonException;
use Core\Json\JsonMessage;
use Core\Repository\Vendas\Vendedor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class ExcluirHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            BD::startTransaction('Vendas');

            $id = $request->getAttribute('id');

            Vendedor::delete($id);

            BD::commit('Vendas');

            return new JsonMessage([
                'id' => $id
            ]);
        } catch (\Exception $ex) {
            BD::rollback('Dashboard');

            return new JsonException($ex);
        }
    }
}
