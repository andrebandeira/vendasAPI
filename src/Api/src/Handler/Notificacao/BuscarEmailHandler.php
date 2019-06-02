<?php

declare(strict_types=1);

namespace Api\Handler\Notificacao;

use Core\Handler\MainHandler;
use Core\Json\JsonException;
use Core\Json\JsonMessage;
use Core\Repository\Vendas\Config;
use Core\Repository\Vendas\Venda;
use Core\Util\MailUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class BuscarEmailHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            $email = Config::email();
            return new JsonMessage(
                $email
            );
        } catch (\Exception $ex) {
            return new JsonException($ex);
        }
    }
}
