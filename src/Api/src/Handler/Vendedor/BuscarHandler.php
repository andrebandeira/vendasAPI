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


class BuscarHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $id = $request->getAttribute('id');

            $dataInicio = $this->getQuery($request, 'data-inicio');
            $dataFim = $this->getQuery($request, 'data-fim');

            $vendedores = Vendedor::buscaVendedor($id, $dataInicio, $dataFim);

            $data = [];

            foreach ($vendedores as $vendedor) {
                $data[] = [
                    'id' => $vendedor->ID,
                    'nome' => $vendedor->NOME,
                    'email' => $vendedor->EMAIL,
                    'comissao' => number_format(floatval($vendedor->COMISSAO),2, ',', '.')
                ];
            }

            return new JsonMessage(
                $data
            );
        } catch (\Exception $ex) {
            return new JsonException($ex);
        }
    }
}
