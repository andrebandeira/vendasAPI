<?php

declare(strict_types=1);

namespace Api\Handler\Venda;

use Core\BD\BD;
use Core\Handler\MainHandler;
use Core\Json\JsonException;
use Core\Json\JsonMessage;
use Core\Model\Vendas\Venda;
use Core\Model\Vendas\Vendedor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class BuscarHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $id = $request->getAttribute('id');

            $data = [];

            if ($id) {
                $venda = Venda::find([
                    'ID' => $id
                ]);

                $data = [
                    'id' => $venda->ID,
                    'nome' => $venda->VENDEDOR_NOME,
                    'email' => $venda->VENDEDOR_EMAIL,
                    'comissao' => $venda->COMISSAO,
                    'valor' => $venda->VALOR,
                    'data' => $venda->DATA_HORA
                ];
            } else {
                $vendas = Venda::getAll();
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
            }

            return new JsonMessage([
                $data
            ]);
        } catch (\Exception $ex) {
            BD::rollback('Dashboard');

            return new JsonException($ex);
        }
    }
}
