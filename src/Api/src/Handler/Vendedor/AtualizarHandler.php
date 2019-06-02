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


class AtualizarHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            BD::startTransaction('Vendas');

            $id = $request->getAttribute('id');

            $nome = $this->getPost($request, 'nome');
            $email = $this->getPost($request, 'email');

            $vendedor = Vendedor::update($id, $nome, $email);

            BD::commit('Vendas');

            return new JsonMessage([
                'id' => $vendedor->ID,
                'nome' => $vendedor->NOME,
                'email' => $vendedor->EMAIL
            ]);
        } catch (\Exception $ex) {
            BD::rollback('Dashboard');

            return new JsonException($ex);
        }
    }
}
