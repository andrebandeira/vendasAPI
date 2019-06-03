<?php

declare(strict_types=1);

namespace Api\Handler\Dashboard;

use Core\BD\BD;
use Core\Exception\MainException;
use Core\Handler\MainHandler;
use Core\Json\JsonException;
use Core\Json\JsonMessage;
use Core\Repository\Vendas\Config;
use Core\Repository\Vendas\Venda;
use Core\Util\MailUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class ComissaoDia extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $vendas = Venda::comissaoDia();

            $data = [];

            foreach ($vendas as $venda) {
                $dia = $venda->DIA;

                if ($dia) {
                    $dia = new \DateTime($dia);
                    $dia = $dia->format('d/m/Y');
                }

                $data[] = [
                    'referencia' => $dia,
                    'valor' => $venda->COMISSAO
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
