<?php

declare(strict_types=1);

namespace Api\Handler\Venda;

use Core\BD\BD;
use Core\Handler\MainHandler;
use Core\Json\JsonException;
use Core\Json\JsonMessage;
use Core\Repository\Vendas\Venda;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class AtualizarHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            BD::startTransaction('Vendas');

            $id = $request->getAttribute('id');

            $vendedor = $this->getPost($request, 'vendedor');
            $valor = $this->getPost($request, 'valor');
            $data = $this->getPost($request, 'data');
            $comissao = $this->getPost($request, 'comissao');

            $venda = Venda::update($id, $vendedor, $valor, $data, $comissao);

            BD::commit('Vendas');

            return new JsonMessage([
                'id' => $venda->ID,
                'nome' => $venda->VENDEDOR_NOME,
                'email' => $venda->VENDEDOR_EMAIL,
                'comissao' => $venda->COMISSAO,
                'valor' => $venda->VALOR,
                'data' => $venda->DATA_HORA
            ]);
        } catch (\Exception $ex) {
            BD::rollback('Dashboard');

            return new JsonException($ex);
        }
    }
}
