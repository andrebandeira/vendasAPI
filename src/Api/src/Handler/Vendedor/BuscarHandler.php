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

            $data = [];

            if ($id) {
                $vendedor = Vendedor::find([
                    'ID' => $id
                ]);

                $data = [
                    'id' => $vendedor->ID,
                    'nome' => $vendedor->NOME,
                    'email' => $vendedor->EMAIL
                ];
            } else {
                $vendedores = Vendedor::getAll();
                foreach ($vendedores as $vendedor) {
                    $data [] = [
                        'id' => $vendedor->ID,
                        'nome' => $vendedor->NOME,
                        'email' => $vendedor->EMAIL
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
