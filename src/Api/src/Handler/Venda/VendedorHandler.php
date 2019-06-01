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


class VendedorHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            BD::startTransaction('Vendas');

            $vendedor = $request->getAttribute('id');

            $data = [];

            $vendas = Venda::findByVendedor($vendedor);

            foreach ($vendas as $venda) {
                $data[] = [
                    'id' => $venda->ID,
                    'nome' => $venda->VENDEDOR_NOME,
                    'email' => $venda->VENDEDOR_EMAIL,
                    'comissao' => $venda->COMISSAO,
                    'valor' => $venda->VALOR,
                    'data' => $venda->DATA_HORA
                ];
            }

            BD::commit('Vendas');

            return new JsonMessage([
                $data
            ]);
        } catch (\Exception $ex) {
            BD::rollback('Dashboard');

            return new JsonException($ex);
        }
    }
}
