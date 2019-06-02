<?php

declare(strict_types=1);

namespace Api\Handler\Venda;

use Core\BD\BD;
use Core\Handler\MainHandler;
use Core\Json\JsonException;
use Core\Json\JsonMessage;
use Core\Repository\Vendas\Venda;
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

            $vendas = Venda::buscaVenda($id, $dataInicio, $dataFim);

            $data = [];

            foreach ($vendas as $venda) {
                $dataHora = $venda->DATA_HORA;

                if ($dataHora) {
                    $dataHora = new \DateTime($dataHora);
                    $dataHora = $dataHora->format('d/m/Y H:i:s');
                }

                $data[] = [
                    'id' => $venda->ID,
                    'nome' => $venda->NOME,
                    'email' => $venda->EMAIL,
                    'comissao' => number_format(floatval($venda->COMISSAO),2, ',', '.'),
                    'valor' => number_format(floatval($venda->VALOR),2, ',', '.'),
                    'data' => $dataHora
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
